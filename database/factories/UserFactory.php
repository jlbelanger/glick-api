<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
	/**
	 * The current password being used by the factory.
	 *
	 * @var string
	 */
	protected static ?string $password;

	/**
	 * Defines the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition() : array
	{
		return [
			'username' => 'foo',
			'email' => 'foo@example.com',
			'email_verified_at' => now(),
			'password' => static::$password ??= Hash::make('password'),
			'remember_token' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZabcdefgh',
		];
	}
}
