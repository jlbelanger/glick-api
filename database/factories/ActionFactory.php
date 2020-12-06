<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = Action::class;

	/**
	 * Defines the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'action_type_id' => ActionType::factory(),
			'start_date' => '2001-02-03 04:05:06',
		];
	}
}
