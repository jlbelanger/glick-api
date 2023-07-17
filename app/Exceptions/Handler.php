<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Jlbelanger\Tapioca\Exceptions\JsonApiException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<Throwable>>
	 */
	protected $dontReport = [
		JsonApiException::class,
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array<int, string>
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
		'new_password',
		'new_password_confirmation',
	];

	/**
	 * Registers the exception handling callbacks for the application.
	 *
	 * @return void
	 */
	public function register()
	{
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
		$this->renderable(function (MethodNotAllowedHttpException $e) {
			return response()->json(['errors' => [['title' => 'URL does not exist.', 'status' => '404', 'detail' => 'Method not allowed.']]], 404);
		});

		$this->renderable(function (JsonApiException $e) {
			return response()->json(['errors' => $e->getErrors()], $e->getCode());
		});

		$this->renderable(function (HttpException $e) {
			return response()->json(['errors' => [['title' => $e->getMessage(), 'status' => $e->getStatusCode()]]], $e->getStatusCode());
		});

		$this->renderable(function (ThrottleRequestsException $e) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
			return response()->json(['errors' => [['title' => 'Please wait before retrying.', 'status' => '429']]], 429);
		});

		$this->renderable(function (Throwable $e) {
			$error = ['title' => 'There was an error connecting to the server.', 'status' => '500'];
			if (config('app.debug')) {
				$error['detail'] = $e->getMessage();
				$error['meta'] = [
					'exception' => get_class($e),
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'trace' => $e->getTrace(),
				];
			}
			return response()->json(['errors' => [$error]], 500);
		});
	}
}
