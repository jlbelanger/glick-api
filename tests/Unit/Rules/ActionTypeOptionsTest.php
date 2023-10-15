<?php

namespace Tests\Unit\Rules;

use App\Models\ActionType;
use App\Rules\ActionTypeOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionTypeOptionsTest extends TestCase
{
	use RefreshDatabase;

	public function passesProvider() : array
	{
		return [
			'with an existing button with options' => [[
				'actionType' => ['field_type' => 'button'],
				'data' => [],
				'value' => [
					'data' => [
						['id' => 'temp-1', 'type' => 'options'],
					],
				],
				'expected' => true,
			]],
			'with an existing button with no options' => [[
				'actionType' => ['field_type' => 'button'],
				'data' => [],
				'value' => null,
				'expected' => true,
			]],
			'with an existing number with options' => [[
				'actionType' => ['field_type' => 'number'],
				'data' => [],
				'value' => [
					'data' => [
						['id' => 'temp-1', 'type' => 'options'],
					],
				],
				'expected' => false,
			]],
			'with an existing number with no options' => [[
				'actionType' => ['field_type' => 'number'],
				'data' => [],
				'value' => null,
				'expected' => true,
			]],
			'with a new button with options' => [[
				'actionType' => [],
				'data' => ['attributes' => ['field_type' => 'button']],
				'value' => [
					'data' => [
						['id' => 'temp-1', 'type' => 'options'],
					],
				],
				'expected' => true,
			]],
			'with a new button with no options' => [[
				'actionType' => [],
				'data' => ['attributes' => ['field_type' => 'button']],
				'value' => null,
				'expected' => true,
			]],
			'with a new number with options' => [[
				'actionType' => [],
				'data' => ['attributes' => ['field_type' => 'number']],
				'value' => [
					'data' => [
						['id' => 'temp-1', 'type' => 'options'],
					],
				],
				'expected' => false,
			]],
			'with a new number with no options' => [[
				'actionType' => [],
				'data' => ['attributes' => ['field_type' => 'number']],
				'value' => null,
				'expected' => true,
			]],
		];
	}

	/**
	 * @dataProvider passesProvider
	 */
	public function testPasses(array $args) : void
	{
		$args['actionType'] = ActionType::factory()->create($args['actionType']);
		$rule = new ActionTypeOptions($args['actionType'], $args['data']);
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
