<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
	/**
	 * Defines the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition() : array
	{
		return [
			'action_type_id' => \App\Models\ActionType::factory(),
			'label' => 'Foo',
		];
	}
}
