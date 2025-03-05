<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (env('APP_DOWN', false)) {
            if (auth()->user()) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/');
            }

            abort(503, 'Situs sedang dalam perbaikan.');
        }

        return $next($request);
    }
}
