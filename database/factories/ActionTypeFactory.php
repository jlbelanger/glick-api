<?php

namespace Database\Factories;

use App\Models\ActionType;
use App\Models\User;
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
			'user_id' => User::factory(),
			'label' => 'Foo',
			'field_type' => 'button',
		];
	}
}
