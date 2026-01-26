<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar_upload' => 'nullable|image|max:2048',
            'telefono' => 'nullable|string|max:20',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->telefono = $request->telefono;

        // LÓGICA DE AVATAR

        // Caso A: Eligió un PRESET (avatar_1.png, etc.)
        if ($request->filled('avatar_preset')) {
            // Si antes tenía una foto SUBIDA (custom), la borramos para limpiar basura
            // Sabemos que es custom si NO empieza con 'avatar_' y NO es url externa
            if ($user->avatar && !str_starts_with($user->avatar, 'avatar_') && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->avatar_preset; // Guardamos "avatar_1.png"
        }

        // Caso B: Subió una imagen NUEVA
        if ($request->hasFile('avatar_upload')) {
            // Borramos la anterior si era custom
            if ($user->avatar && !str_starts_with($user->avatar, 'avatar_') && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Guardamos la nueva en storage
            $path = $request->file('avatar_upload')->store('avatars', 'public');
            $user->avatar = $path; // Guardamos "avatars/xyz.jpg"
        }

        // Password...
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            ]);
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}