<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
	/**
	 * Handles an incoming request.
	 *
	 * @param  Request     $request
	 * @param  Closure     $next
	 * @param  string|null ...$guards
	 * @return Response
	 */
	public function handle(Request $request, Closure $next, string ...$guards) : Response
	{
		$guards = empty($guards) ? [null] : $guards;

		foreach ($guards as $guard) {
			if (Auth::guard($guard)->check()) {
				return redirect('/');
			}
		}

		return $next($request);
	}
}
