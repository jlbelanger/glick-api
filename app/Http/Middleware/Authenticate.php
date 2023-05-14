<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jlbelanger\Tapioca\Exceptions\JsonApiException;

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
			throw JsonApiException::generate([['title' => 'You are not logged in.', 'status' => '401']], 401);
		}

		return $next($request);
	}
}
