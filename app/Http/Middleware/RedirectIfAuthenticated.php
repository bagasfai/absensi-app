<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next, string ...$guards): Response
	{
		$guards = empty($guards) ? [null] : $guards;

		foreach ($guards as $guard) {
			if (Auth::guard($guard)->check()) {
				$requestedPath = request()->path();
				Log::info("Requested Path: $requestedPath");

				if ($requestedPath === 'panel') {
					Log::info('Redirecting to HOMEADMIN');
					return redirect(RouteServiceProvider::HOMEADMIN);
				} else {
					Log::info('Redirecting to HOME');
					return redirect(RouteServiceProvider::HOME);
				}
			}
		}

		return $next($request);
	}
}
