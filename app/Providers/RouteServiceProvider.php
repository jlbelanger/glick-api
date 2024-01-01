<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * The path to your application's "home" route.
	 *
	 * Typically, users are redirected here after authentication.
	 *
	 * @var string
	 */
	public const HOME = '/';

	/**
	 * Defines your route model bindings, pattern filters, and other route configuration.
	 *
	 * @return void
	 */
	public function boot() : void
	{
		$this->configureRateLimiting();

		$this->routes(
			function () {
				Route::middleware('api')
					->group(base_path('routes/api.php'));
			}
		);
	}

	/**
	 * Configures the rate limiters for the application.
	 *
	 * @return void
	 */
	protected function configureRateLimiting()
	{
		RateLimiter::for('auth', function (Request $request) {
			return Limit::perMinute(config('auth.throttle_max_attempts_auth'))->by($request->ip());
		});

		RateLimiter::for('api', function (Request $request) {
			return Limit::perMinute(config('auth.throttle_max_attempts_api'))->by($request->ip());
		});
	}
}
