<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleController extends Controller
{
    // Redirige al usuario a la pantalla de Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account']) // <-- ESTA ES LA MAGIA
            ->redirect(); // <-- El único punto y coma va hasta el final
    }

    // Google nos devuelve al usuario aquí
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. Buscamos al usuario por Google ID o Email (incluyendo la papelera)
            $findUser = User::withTrashed()
                            ->where('google_id', $googleUser->id)
                            ->orWhere('email', $googleUser->email)
                            ->first();

            if ($findUser) {
                // 2. MODO ESTRICTO: ¿Está eliminado lógicamente?
                if ($findUser->trashed()) {
                    // ¡Acceso denegado! Lo mandamos al login con un mensaje de error
                    return redirect()->route('login')->withErrors([
                        'email' => __('auth/messages.account_deleted')
                    ]);
                }

                // 3. Si no está en la papelera, vemos si le falta vincular su google_id
                if (empty($findUser->google_id)) {
                    $findUser->google_id = $googleUser->id;
                    if (empty($findUser->avatar) && !empty($googleUser->avatar)) {
                        $findUser->avatar = $googleUser->avatar;
                    }
                    $findUser->save();
                }

                // 4. Lo logueamos normalmente
                Auth::login($findUser);
                return redirect()->route('home')->with('success', __('auth/messages.google_welcome'));
                
            } else {
                // 5. Si no existe ni vivo ni muerto, lo registramos como nuevo cliente
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null, 
                    'rol' => 'cliente',
                    'avatar' => $googleUser->avatar
                ]);
                
                Auth::login($newUser);
                return redirect()->route('home')->with('success', __('auth/messages.google_registered'));
            }

        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['error' => __('auth/messages.google_error')]);
        }
    }
}