<?php

namespace Database\Factories;

use App\Models\ActionType;
use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = Option::class;

	/**
	 * Defines the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'action_type_id' => ActionType::factory(),
			'label' => 'Foo',
		];
	}
}
