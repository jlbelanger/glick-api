<?php

namespace Database\Factories;

use App\Models\Action;
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
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'action_type_id' => factory(App\Models\ActionType::class)->create()->id,
			'start_date' => $this->faker->dateTime(),
			'end_date' => $this->faker->dateTime(),
			'value' => $this->faker->randomDigit,
		];
	}
}
