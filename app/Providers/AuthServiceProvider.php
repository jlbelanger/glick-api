<?php

namespace App\Providers;

use App\Policies\ActionPolicy;
use App\Policies\ActionTypePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		'App\Models\Action' => ActionPolicy::class,
		'App\Models\ActionType' => ActionTypePolicy::class,
		'App\Models\User' => UserPolicy::class,
	];

	/**
	 * Registers any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();
	}
}
