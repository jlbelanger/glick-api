<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActionTypeFactory extends Factory
{
	/**
	 * Defines the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition() : array
	{
		return [
			'user_id' => \App\Models\User::factory(),
			'label' => 'Foo',
			'field_type' => 'button',
		];
	}
}
