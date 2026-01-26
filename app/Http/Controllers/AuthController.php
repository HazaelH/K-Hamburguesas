<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirección inteligente según el rol
            $rol = Auth::user()->rol;
            
            if ($rol === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($rol === 'empleado') {
                return redirect()->route('employee.panel');
            } else {
                // Cliente normal
                return redirect()->route('home');
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput(); // Devuelve el email para que no lo tenga que escribir de nuevo
    }

    // --- REGISTRO (Nuevo) ---
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' busca un campo password_confirmation
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'cliente', // Por defecto todos son clientes
        ]);

        Auth::login($user); // Iniciar sesión automáticamente

        return redirect()->route('home');
    }

    // --- LOGOUT ---
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}