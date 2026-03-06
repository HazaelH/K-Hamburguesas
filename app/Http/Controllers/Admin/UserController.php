<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules; // <--- IMPORTANTE: Importamos las reglas extendidas

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('ver_bajas')) {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('id', $search);
            });
        }

        if ($request->filled('rol')) {
            if ($request->rol === 'equipo') {
                $query->whereIn('rol', ['admin', 'mesero', 'cajero', 'cocinero', 'repartidor']);
            } else {
                $query->where('rol', $request->rol);
            }
        }

        $users = $query->paginate(10);
        
        $stats = [
            'total' => User::count(),
            'admins' => User::where('rol', 'admin')->count(),
            'clientes' => User::where('rol', 'cliente')->count(),
        ];         

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {  
        // VALIDACIÓN ESTRICTA
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'rol' => 'required|in:admin,cliente,mesero,cajero,cocinero,repartidor',
            'foto_custom' => 'nullable|image|max:2048',
            'avatar_option' => 'nullable|string'
        ], [
            'email.unique' => __('auth/messages.email_unique'),
            'password.min' => __('auth/messages.password_min'),
            'password.mixed' => __('auth/messages.password_mixed'),
            'password.numbers' => __('auth/messages.password_numbers'),
            'password.symbols' => __('auth/messages.password_symbols'),
            'password.confirmed' => __('auth/messages.password_confirmed'),
        ]);

        $avatarPath = null;

        if ($request->hasFile('foto_custom')) {
            $avatarPath = $request->file('foto_custom')->store('users', 'public');
        } elseif ($request->filled('avatar_option')) {
            $avatarPath = $request->avatar_option;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'avatar' => $avatarPath,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', __('admin/users/messages.create_success'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // VALIDACIÓN ESTRICTA (Permitiendo campos nulos en password)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', Rule::unique('users')->ignore($user->id)],
            'rol' => 'required|in:admin,cliente,mesero,cajero,cocinero,repartidor',
            'password' => [
                'nullable', // Nullable porque si no la quiere cambiar, la deja en blanco
                'confirmed',
                Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'foto_custom' => 'nullable|image|max:2048',
            'avatar_option' => 'nullable|string'
        ], [
            'email.unique' => __('auth/messages.email_unique'),
            'password.min' => __('auth/messages.password_min'),
            'password.mixed' => __('auth/messages.password_mixed'),
            'password.numbers' => __('auth/messages.password_numbers'),
            'password.symbols' => __('auth/messages.password_symbols'),
            'password.confirmed' => __('auth/messages.password_confirmed'),
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->rol = $request->rol;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto_custom')) {
            if ($user->avatar && str_starts_with($user->avatar, 'users/')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('foto_custom')->store('users', 'public');
        } elseif ($request->filled('avatar_option')) {
            if ($user->avatar && str_starts_with($user->avatar, 'users/')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->avatar_option;
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', __('admin/users/messages.update_success'));
    }

    public function destroy($id)
    {
        if (Auth::id() == $id) {
            return back()->with('error', __('admin/users/messages.cannot_delete_self'));
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('admin/users/messages.delete_success'));
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index', ['ver_bajas' => 1])->with('success', __('admin/users/messages.restore_success'));
    }

    public function forceDestroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        try {
            $user->forceDelete();
            
            if ($user->avatar && str_starts_with($user->avatar, 'users/')) {
                Storage::disk('public')->delete($user->avatar);
            }

            return redirect()->route('admin.users.index', ['ver_bajas' => 1])
                             ->with('success', __('admin/users/messages.force_delete_success'));

        } catch (QueryException $e) {
            if ($e->getCode() == "23000") {
                return redirect()->route('admin.users.index', ['ver_bajas' => 1])
                                 ->with('error', __('admin/users/messages.force_delete_error_fk'));
            }
            
            return redirect()->route('admin.users.index', ['ver_bajas' => 1])
                             ->with('error', __('admin/users/messages.force_delete_error_db'));
        }
    }
}