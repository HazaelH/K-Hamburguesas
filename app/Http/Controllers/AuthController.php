<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    // --- LOGIN ---
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validaciones
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc,dns', 'max:255'], 
            'password' => ['required', 'string'],
        ], [
            'email.required' => __('auth/messages.email_required'),
            'email.email' => __('auth/messages.email_invalid'),
            'password.required' => __('auth/messages.password_required'),
        ]);

        // 2. Rate Limiting
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        // 3. Intento de Login
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            return match ($user->rol) {
                'admin'      => redirect()->route('admin.dashboard'),
                'mesero'     => redirect()->route('employee.pos'),          
                'cocinero'   => redirect()->route('kitchen.live'),          
                'cajero'     => redirect()->route('employee.orders.index'), 
                'repartidor' => redirect()->route('delivery.scan'),         
                default      => redirect()->route('home'),                  
            };
        } 

        // 4. Fallo
        RateLimiter::hit($throttleKey);

        return back()->withErrors([
            'email' => __('auth/messages.credentials_mismatch'),
        ])->withInput($request->only('email'));
    }

    // --- REGISTRO ---
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
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
        ], [
            'email.unique' => __('auth/messages.email_unique'),
            'password.min' => __('auth/messages.password_min'),
            'password.mixed' => __('auth/messages.password_mixed'),
            'password.numbers' => __('auth/messages.password_numbers'),
            'password.symbols' => __('auth/messages.password_symbols'),
            'password.confirmed' => __('auth/messages.password_confirmed'),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'cliente',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    // --- LOGOUT ---
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // --- RECUPERACIÓN DE CONTRASEÑA ---
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __('auth/messages.reset_link_sent'));
        }

        return back()->withErrors(['email' => __('auth/messages.user_not_found')]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'email.required' => __('auth/messages.email_required'),
            'password.required' => __('auth/messages.password_required'),
            'password.min' => __('auth/messages.password_min'),
            'password.mixed' => __('auth/messages.password_mixed'),
            'password.numbers' => __('auth/messages.password_numbers'),
            'password.symbols' => __('auth/messages.password_symbols'),
            'password.confirmed' => __('auth/messages.password_confirmed'),
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => \Illuminate\Support\Facades\Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));
                
                $user->save();
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __('auth/messages.password_reset_success'));
        }

        return back()->withErrors(['email' => __('auth/messages.invalid_token')]);
    }
}