<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Jlbelanger\LaravelJsonApi\Exceptions\JsonApiException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->renderable(function (MethodNotAllowedHttpException $e) {
			return response()->json(['errors' => [['title' => 'URL does not exist.', 'status' => '404', 'detail' => 'Method not allowed.']]], 404);
		});
		$this->renderable(function (JsonApiException $e) {
			return response()->json(['errors' => [$e->getError()]], $e->getCode());
		});
		$this->renderable(function (HttpException $e) {
			return response()->json(['errors' => [['title' => $e->getMessage(), 'status' => $e->getStatusCode()]]], $e->getStatusCode());
		});
	}
}
