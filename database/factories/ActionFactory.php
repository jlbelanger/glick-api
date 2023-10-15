<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActionFactory extends Factory
{
	/**
	 * Defines the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'action_type_id' => \App\Models\ActionType::factory(),
			'start_date' => '2001-02-03 04:05:06',
		];
	}
}
