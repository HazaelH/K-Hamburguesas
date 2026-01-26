<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // 1. LISTAR USUARIOS
    public function index(Request $request)
    {
        // Iniciamos la consulta
        $query = User::query();

        // A. Filtro por Texto (Nombre, Email o ID)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        // B. Filtro por Rol
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        // Ordenamos y paginamos (manteniendo los filtros en los links de paginación)
        $users = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // 2. FORMULARIO CREAR
    public function create()
    {
        return view('admin.users.create');
    }

    // 3. GUARDAR NUEVO
    public function store(Request $request)
    {  
    $request->validate([
        'name' => 'required...',
        // ... otras reglas
        'foto_custom' => 'nullable|image|max:2048', // Validar imagen
        'avatar_option' => 'nullable|string'
    ]);

    // Lógica del Avatar
    $avatarPath = null;

    // 1. Prioridad: ¿Subió una foto personalizada?
    if ($request->hasFile('foto_custom')) {
        // Guardamos en storage/app/public/users
        $path = $request->file('foto_custom')->store('users', 'public');
        $avatarPath = $path; // ej: users/xyz123.jpg
    } 
    // 2. Si no, ¿Eligió un avatar predeterminado?
    elseif ($request->filled('avatar_option')) {
        $avatarPath = $request->avatar_option; // ej: avatar_1.png
    }

    User::create([
        'name' => $request->name,
        // ... otros campos
        'avatar' => $avatarPath, // Guardamos la ruta o el nombre
    ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario registrado correctamente.');
    }

    // 4. FORMULARIO EDITAR
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // 5. ACTUALIZAR
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'rol' => 'required|in:admin,empleado,cliente',
            'password' => 'nullable|min:6',
            'foto_custom' => 'nullable|image|max:2048',
            'avatar_option' => 'nullable|string'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->rol = $request->rol;

        // 1. Contraseña (Solo si escribió una nueva)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 2. Avatar (Lógica corregida)
        // Prioridad A: Subió foto nueva
        if ($request->hasFile('foto_custom')) {
            // (Opcional) Aquí podrías borrar la foto anterior si existiera
            // if ($user->avatar && str_starts_with($user->avatar, 'users/')) Storage::disk('public')->delete($user->avatar);
            
            $path = $request->file('foto_custom')->store('users', 'public');
            $user->avatar = $path;
        } 
        // Prioridad B: Eligió avatar predeterminado
        elseif ($request->filled('avatar_option')) {
            $user->avatar = $request->avatar_option;
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // 6. ELIMINAR
    public function destroy($id)
    {
        // Evitar auto-suicidio del admin
        if (Auth::id() == $id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta mientras estás logueado.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado del sistema.');
    }
}