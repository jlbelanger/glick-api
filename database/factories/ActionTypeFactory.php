<?php

namespace Database\Factories;

use App\Models\ActionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionTypeFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = ActionType::class;

	/**
	 * Defines the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'user_id' => factory(App\Models\User::class)->create()->id,
			'label' => ucfirst($this->faker->word),
			'is_continuous' => $this->faker->randomElement(['0', '1']),
			'field_type' => $this->faker->randomElement(['int', 'float', 'string']),
			'suffix' => null,
			'options' => null,
			'order_num' => $this->faker->randomDigit,
		];
	}
}
