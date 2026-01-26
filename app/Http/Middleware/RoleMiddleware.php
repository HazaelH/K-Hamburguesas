<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <--- ¡ESTO FALTABA!
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Verificar si está logueado
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. Verificar el rol
        // Si el rol del usuario NO es el requerido
        if ($user->rol !== $role) {
            
            // "Super Admin": Permitimos pasar al admin siempre (según tu lógica)
            // EXCEPTO si intentamos restringir algo exclusivo de cliente, 
            // pero por ahora está bien que el admin vea todo.
            if ($user->rol !== 'admin') {
                abort(403, 'No tienes permisos para acceder a esta sección.');
            }
        }

        return $next($request);
    }
}