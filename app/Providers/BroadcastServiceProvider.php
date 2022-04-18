<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
	/**
	 * Bootstraps any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Broadcast::routes();

		include base_path('routes/channels.php');
	}
}
