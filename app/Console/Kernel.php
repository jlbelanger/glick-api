<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		Commands\ResetAuth::class,
	];

	/**
	 * Defines the application's command schedule.
	 *
	 * @param  Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule) : void
	{
	}

	/**
	 * Registers the commands for the application.
	 *
	 * @return void
	 */
	protected function commands() : void
	{
		$this->load(__DIR__ . '/Commands');

		include base_path('routes/console.php');
	}
}
