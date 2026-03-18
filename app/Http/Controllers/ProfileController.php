<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeleteAccountMail;
use App\Models\UserAddress;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. VALIDACIÓN DE DATOS GENERALES
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'avatar_upload' => 'nullable|image|max:2048',
            'telefono' => 'nullable|string|max:20',
        ]);

        $user->name = $request->name;
        $user->telefono = $request->telefono;

        // 2. LÓGICA DE AVATAR
        if ($request->filled('avatar_preset')) {
            if ($user->avatar && !str_starts_with($user->avatar, 'avatar_') && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->avatar_preset; 
        }

        if ($request->hasFile('avatar_upload')) {
            if ($user->avatar && !str_starts_with($user->avatar, 'avatar_') && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar_upload')->store('avatars', 'public');
            $user->avatar = $path; 
        }

        // 3. LÓGICA DE CONTRASEÑA CON VALIDACIÓN ESTRICTA Y MENSAJES PERSONALIZADOS
        if ($request->filled('password')) {
            $request->validate([
                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ],
            ], [
                // Inyectamos los mensajes traducidos si el usuario falla la regla
                'password.min' => __('profile/messages.password_min'),
                'password.mixed' => __('profile/messages.password_mixed'),
                'password.numbers' => __('profile/messages.password_numbers'),
                'password.symbols' => __('profile/messages.password_symbols'),
                'password.confirmed' => __('profile/messages.password_confirmed'),
            ]);

            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', __('profile/messages.update_success'));
    }

    // 1. EL USUARIO PIDE BORRAR SU CUENTA DESDE LA PÁGINA (Botón Rojo)
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Generamos un enlace súper seguro, firmado con la llave secreta de Laravel
        // y le decimos que expira en exactamente 30 minutos.
        $url = URL::temporarySignedRoute(
            'profile.confirm_delete', 
            now()->addMinutes(30), 
            ['id' => $user->id]
        );

        // Disparamos el correo
        Mail::to($user->email)->send(new DeleteAccountMail($url));

        // Lo regresamos al perfil con el aviso
        return back()->with('success', __('profile/messages.delete_email_sent'));
    }

    // 2. EL USUARIO HACE CLIC EN EL CORREO
    public function confirmDeletion(Request $request, $id)
    {
        // Si el enlace fue alterado por un hacker o ya pasaron 30 min, lo rebotamos.
        if (! $request->hasValidSignature()) {
            abort(403, __('profile/messages.invalid_signature'));
        }

        $user = \App\Models\User::find($id);

        if ($user) {
            // Si tiene sesión iniciada en este navegador, lo sacamos.
            if (Auth::check() && Auth::id() == $user->id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            // Adiós vaquero (SoftDelete)
            $user->delete();

            return redirect('/')->with('success', __('profile/messages.delete_success'));
        }

        return redirect('/');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'alias' => 'required|string|max:50',
            'telefono' => 'required|string|max:20',
            'calle' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'codigo_postal' => 'required|string|max:10',
            'colonia' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'referencias' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        
        // Si es la primera dirección, se hace principal por defecto
        $esPrimera = $user->addresses()->count() === 0;

        $user->addresses()->create([
            'alias' => $request->alias,
            'codigo_pais' => '+52', // Puedes hacerlo dinámico si tienes selector de país
            'telefono' => $request->telefono,
            'calle' => $request->calle,
            'numero' => $request->numero,
            'codigo_postal' => $request->codigo_postal,
            'colonia' => $request->colonia,
            'municipio' => $request->municipio,
            'estado' => $request->estado,
            'referencias' => $request->referencias,
            'is_default' => $esPrimera
        ]);

        return back()->with('success', __('profile/edit.address_added'));
    }

    public function destroyAddress($id)
    {
        $direccion = Auth::user()->addresses()->findOrFail($id);
        $direccion->delete();

        return back()->with('success', __('profile/edit.address_deleted'));
    }

    public function setDefaultAddress($id)
    {
        $user = Auth::user();
        $nuevaPrincipal = $user->addresses()->findOrFail($id);

        // Quitamos el default a todas las demás
        $user->addresses()->update(['is_default' => false]);
        
        // Se lo ponemos a esta
        $nuevaPrincipal->update(['is_default' => true]);

        return back()->with('success', __('profile/edit.address_default'));
    }
    
    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'alias' => 'required|string|max:50',
            'telefono' => 'required|string|max:20',
            'calle' => 'required|string|max:100',
            'numero' => 'required|string|max:20',
            'codigo_postal' => 'required|string|max:10',
            'colonia' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'referencias' => 'nullable|string|max:255',
        ]);

        $direccion = Auth::user()->addresses()->findOrFail($id);
        $direccion->update($request->all());

        return back()->with('success', __('profile/edit.address_updated', ['default' => 'Dirección actualizada correctamente.']));
    }
}