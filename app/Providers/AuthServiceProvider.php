<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array<class-string, class-string>
	 */
	protected $policies = [];

	/**
	 * Registers any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();

		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
		ResetPassword::toMailUsing(function ($notifiable, $token) {
			$url = config('app.frontend_url') . str_replace('/auth/', '/', URL::temporarySignedRoute(
				'password.update',
				Carbon::now()->addMinutes(Config::get('auth.passwords.users.expire', 60)),
				['token' => $token],
				false
			));
			return (new MailMessage)
				->subject('[' . config('app.name') . '] Reset Password')
				->line('You are receiving this email because we received a password reset request for your account.')
				->action('Reset Password', $url)
				->line('This link will expire in ' . Config::get('auth.passwords.users.expire', 60) . ' minutes.')
				->line('If you did not request a password reset, no further action is required.');
		});

		// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
		VerifyEmail::toMailUsing(function ($notifiable, $url) {
			return (new MailMessage)
				->subject('[' . config('app.name') . '] Verify Email Address')
				->line('Please click the button below to verify your email address.')
				->action('Verify Email Address', $url)
				->line('This link will expire in ' . Config::get('auth.verification.expire', 60) . ' minutes.')
				->line('If you did not create an account, no further action is required.');
		});

		VerifyEmail::createUrlUsing(function ($notifiable) {
			return config('app.frontend_url') . str_replace('/auth/', '/', URL::temporarySignedRoute(
				'verification.verify',
				Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
				[
					'id' => $notifiable->getKey(),
					'hash' => sha1($notifiable->getEmailForVerification()),
				],
				false
			));
		});

		Password::defaults(function () {
			return Password::min(8);
		});
	}
}
