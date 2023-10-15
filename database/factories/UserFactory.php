<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
	/**
	 * Defines the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'username' => 'foo',
			'email' => 'foo@example.com',
			'email_verified_at' => now(),
			// The password is 'password'.
			'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
			'remember_token' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZabcdefgh',
		];
	}

	public function unverified()
	{
		return $this->state(function () {
			return [
				'email_verified_at' => null,
			];
		});
	}
}
