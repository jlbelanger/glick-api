<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class ResetAuth extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'auth:reset-admin';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reset admin credentials';

	/**
	 * Executes the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		if (!app()->isLocal()) {
			echo "Error: This is not the local environment.\n";
			return;
		}

		echo "Resetting password...\n";
		$user = User::first();
		$data = [
			'password' => Hash::make('password'),
		];
		if (!$user) {
			$user = new User();
			$data['username'] = 'test';
			$data['email'] = 'test@example.com';
			$data['email_verified_at'] = Carbon::now();
		}
		$user->forceFill($data)->save();
		echo "Success!\n";
	}
}
