<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Jlbelanger\Tapioca\Exceptions\JsonApiException;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		api: __DIR__ . '/../routes/api.php',
		apiPrefix: '',
	)
	->withMiddleware(function (Middleware $middleware) {
		$middleware->appendToGroup('api', [
			\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
		]);

		$middleware->alias([
			'auth' => \App\Http\Middleware\Authenticate::class,
			'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
		]);
	})
	->withExceptions(function (Exceptions $exceptions) {
		$exceptions->dontReport([
			JsonApiException::class,
		]);

		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$exceptions->render(function (InvalidSignatureException $e) {
			if (!url()->signatureHasNotExpired(request())) {
				return response()->json(['errors' => [['title' => __('passwords.expired'), 'status' => '403']]], 403);
			}
			return response()->json(['errors' => [['title' => __('passwords.token'), 'status' => '403']]], 403);
		});

		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$exceptions->render(function (MethodNotAllowedHttpException $e) {
			return response()->json(['errors' => [['title' => 'URL does not exist.', 'status' => '404', 'detail' => 'Method not allowed.']]], 404);
		});

		$exceptions->render(function (NotFoundHttpException $e) {
			return response()->json(['errors' => [['title' => $e->getMessage() ? $e->getMessage() : 'URL does not exist.', 'status' => '404']]], 404);
		});

		$exceptions->render(function (ThrottleRequestsException $e) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
			return response()->json(['errors' => [['title' => 'Please wait before retrying.', 'status' => '429']]], 429);
		});

		$exceptions->render(function (ValidationException $e) {
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

		$exceptions->render(function (HttpException $e) {
			return response()->json(['errors' => [['title' => $e->getMessage(), 'status' => (string) $e->getStatusCode()]]], $e->getStatusCode());
		});

		$exceptions->render(function (JsonApiException $e) {
			return response()->json(['errors' => $e->getErrors()], $e->getCode());
		});

		$exceptions->render(function (Throwable $e) {
			$error = ['title' => 'There was an error connecting to the server.', 'status' => '500'];
			if (config('app.debug')) {
				$error['detail'] = $e->getMessage();
				$error['meta'] = [
					'exception' => get_class($e),
					'file' => $e->getFile(),
					'line' => $e->getLine(),
				];
				if (config('app.env') !== 'testing') {
					$error['meta']['trace'] = $e->getTrace();
				}
			}
			return response()->json(['errors' => [$error]], 500);
		});
	})->create();
