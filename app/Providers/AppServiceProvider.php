<?php

namespace App\Providers;

use App\Http\Kernel;
use App\Models\Action;
use App\Observers\ActionObserver;
use DB;
use Illuminate\Support\ServiceProvider;
use Log;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Registers any application services.
	 *
	 * @return void
	 */
	public function register() : void
	{
	}

	/**
	 * Bootstraps any application services.
	 *
	 * @param  Kernel $kernel
	 * @return void
	 */
	public function boot(Kernel $kernel) : void
	{
		if (config('app.debug')) {
			if (config('logging.database')) {
				DB::listen(function ($q) {
					$trace = debug_backtrace();
					$source = null;
					foreach ($trace as $t) {
						if (!empty($t['file']) && strpos($t['file'], '/vendor/') === false) {
							$source = $t['file'] . ':' . $t['line'];
							break;
						}
					}
					Log::channel('database')->info(json_encode([
						'ms' => $q->time,
						'q' => $q->sql,
						'bindings' => $q->bindings,
						'source' => $source,
					]));
				});
			}
		}

		if ($this->app->environment() !== 'local') {
			$kernel->appendMiddlewareToGroup('api', \Illuminate\Routing\Middleware\ThrottleRequests::class);
		}

		Action::observe(ActionObserver::class);
	}
}
