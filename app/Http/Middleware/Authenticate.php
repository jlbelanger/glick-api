<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
	/**
	 * Handles an incoming request.
	 *
	 * @param  Request     $request
	 * @param  Closure     $next
	 * @param  string|null $guard
	 * @return mixed
	 */
	public function handle(Request $request, Closure $next, $guard = null)
	{
		if (!Auth::guard($guard)->check()) {
			return response()->json(['errors' => [['title' => 'URL does not exist.', 'status' => '404']]], 404);
		}

		return $next($request);
	}
}
