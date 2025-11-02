<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasAtelier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->ateliers()->exists()) {
            if ($request->routeIs('ateliers.create') || $request->routeIs('ateliers.store')) {
                return $next($request);
            }

            return redirect()->route('ateliers.create')
                ->with('info', 'Crie seu ateliê para começar a usar a plataforma.');
        }

        return $next($request);
    }
}
