<?php

namespace Tests\Unit\Rules;

use App\Models\ActionType;
use App\Rules\ActionValueCreate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionValueCreateTest extends TestCase
{
	use RefreshDatabase;

	public function passesProvider() : array
	{
		return [
			'with no action type' => [[
				'actionType' => null,
				'value' => null,
				'expected' => true,
			]],
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
	public function testPasses(array $args) : void
	{
		if ($args['actionType'] !== null) {
			$actionType = ActionType::factory()->create($args['actionType']);
		} else {
			$actionType = null;
		}
		$rule = new ActionValueCreate($actionType);
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
