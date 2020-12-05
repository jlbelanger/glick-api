<?php

namespace App\Providers;

use App\Models\Action;
use App\Observers\ActionObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Registers any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstraps any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		ResetPassword::createUrlUsing(function ($notifiable, string $token) {
			return env('FRONTEND_URL') . '/reset-password/' . $token;
		});

		Action::observe(ActionObserver::class);
	}
}
