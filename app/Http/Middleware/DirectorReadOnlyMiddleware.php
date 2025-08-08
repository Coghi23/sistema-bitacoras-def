<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DirectorReadOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        
        // Si el usuario es director, solo permitir métodos GET (lectura)
        if ($user->hasRole('director')) {
            if (!$request->isMethod('GET')) {
                return response()->json([
                    'error' => 'No tienes permisos para realizar esta acción. Solo tienes acceso de lectura.'
                ], 403);
            }
        }

        return $next($request);
    }
}
