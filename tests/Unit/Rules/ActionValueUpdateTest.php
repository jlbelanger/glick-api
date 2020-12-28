<?php

namespace Tests\Unit\Rules;

use App\Models\Action;
use App\Models\ActionType;
use App\Rules\ActionValueUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionValueUpdateTest extends TestCase
{
	use RefreshDatabase;

	public function passesProvider()
	{
		return [
			'with number action with value set to null' => [[
				'actionType' => ['field_type' => 'number'],
				'value' => null,
				'expected' => false,
			]],
			'with number action with value set to empty string' => [[
				'actionType' => ['field_type' => 'number'],
				'value' => '',
				'expected' => false,
			]],
			'with number action with value set to 0' => [[
				'actionType' => ['field_type' => 'number'],
				'value' => '0',
				'expected' => true,
			]],
			'with number action with value set to number' => [[
				'actionType' => ['field_type' => 'number'],
				'value' => '123',
				'expected' => true,
			]],
			'with button action with value set to null' => [[
				'actionType' => ['field_type' => 'button'],
				'value' => null,
				'expected' => true,
			]],
			'with button action with value set to empty string' => [[
				'actionType' => ['field_type' => 'button'],
				'value' => '',
				'expected' => true,
			]],
			'with button action with value set to 0' => [[
				'actionType' => ['field_type' => 'button'],
				'value' => '0',
				'expected' => false,
			]],
			'with button action with value set to number' => [[
				'actionType' => ['field_type' => 'button'],
				'value' => '123',
				'expected' => false,
			]],
		];
	}

	/**
	 * @dataProvider passesProvider
	 */
	public function testPasses($args)
	{
		$actionType = ActionType::factory()->create($args['actionType']);
		$action = Action::factory()->create(['action_type_id' => $actionType->id]);
		$rule = new ActionValueUpdate($action);
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
