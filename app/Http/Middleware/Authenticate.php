<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Jlbelanger\Tapioca\Exceptions\JsonApiException;

class Authenticate extends Middleware
{
	/**
	 * Handles an unauthenticated user.
	 *
	 * @param  Request $request
	 * @param  array   $guards
	 * @return void
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed, Squiz.Commenting.FunctionComment.TypeHintMissing
	protected function unauthenticated($request, array $guards)
	{
		throw JsonApiException::generate([['title' => 'You are not logged in.', 'status' => '401']], 401);
	}
}
