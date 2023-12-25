<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Jlbelanger\Tapioca\Exceptions\JsonApiException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<Throwable>>
	 */
	protected $dontReport = [JsonApiException::class];

	/**
	 * The list of the inputs that are never flashed to the session on validation exceptions.
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
	public function register() : void
	{
		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$this->renderable(function (InvalidSignatureException $e) {
			if (!url()->signatureHasNotExpired(request())) {
				return response()->json(['errors' => [['title' => __('passwords.expired'), 'status' => '403']]], 403);
			}
			return response()->json(['errors' => [['title' => __('passwords.token'), 'status' => '403']]], 403);
		});

		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$this->renderable(function (MethodNotAllowedHttpException $e) {
			return response()->json(['errors' => [['title' => 'URL does not exist.', 'status' => '404', 'detail' => 'Method not allowed.']]], 404);
		});

		$this->renderable(function (NotFoundHttpException $e) {
			return response()->json(['errors' => [['title' => $e->getMessage() ? $e->getMessage() : 'URL does not exist.', 'status' => '404']]], 404);
		});

		$this->renderable(function (ThrottleRequestsException $e) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
			return response()->json(['errors' => [['title' => 'Please wait before retrying.', 'status' => '429']]], 429);
		});

		$this->renderable(function (ValidationException $e) {
			$output = [];
			$errors = $e->validator->errors()->toArray();
			foreach ($errors as $pointer => $titles) {
				foreach ($titles as $title) {
					$output[] = [
						'title' => $title,
						'source' => [
							'pointer' => '/' . str_replace('.', '/', $pointer),
						],
						'status' => '422',
					];
				}
			}
			return response()->json(['errors' => $output], 422);
		});

		$this->renderable(function (HttpException $e) {
			return response()->json(['errors' => [['title' => $e->getMessage(), 'status' => (string) $e->getStatusCode()]]], $e->getStatusCode());
		});

		$this->renderable(function (JsonApiException $e) {
			return response()->json(['errors' => $e->getErrors()], $e->getCode());
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
