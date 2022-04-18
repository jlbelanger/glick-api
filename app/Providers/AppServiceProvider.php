<?php

namespace App\Providers;

use App\Http\Kernel;
use App\Models\Action;
use App\Observers\ActionObserver;
use DB;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Log;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Registers any application services.
	 *
	 * @return void
	 */
	public function register()
	{
	}

	/**
	 * Bootstraps any application services.
	 *
	 * @param  Kernel $kernel
	 * @return void
	 */
	public function boot(Kernel $kernel)
	{
		if (env('LOG_DATABASE_QUERIES') === '1') {
			DB::listen(function ($query) {
				Log::info($query->sql, $query->bindings, $query->time);
			});
		}

		if ($this->app->environment() !== 'local') {
			$kernel->appendMiddlewareToGroup('api', \Illuminate\Routing\Middleware\ThrottleRequests::class);
		}

		ResetPassword::createUrlUsing(function ($notifiable, string $token) {
			return env('FRONTEND_URL') . '/reset-password/' . $token;
		});

		Action::observe(ActionObserver::class);
	}
}
