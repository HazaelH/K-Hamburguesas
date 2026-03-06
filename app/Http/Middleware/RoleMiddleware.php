<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  (Recibe múltiples roles separados por coma)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar si está logueado
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. Verificar si el rol del usuario está dentro de los roles permitidos en la ruta
        // Como en nuestras rutas ya incluimos al 'admin' explícitamente en los permisos,
        // esta simple validación es suficiente y súper segura.
        if (in_array($user->rol, $roles)) {
            return $next($request);
        }

        // 3. Si no tiene el rol, patada voladora (Error 403)
        abort(403, 'Acceso Denegado: Tu puesto actual (' . strtoupper($user->rol) . ') no tiene permisos para ver esta pantalla.');
    }
}