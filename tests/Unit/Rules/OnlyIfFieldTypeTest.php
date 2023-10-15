<?php

namespace Tests\Unit\Rules;

use App\Models\ActionType;
use App\Rules\OnlyIfFieldType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnlyIfFieldTypeTest extends TestCase
{
	use RefreshDatabase;

	public function passesProvider() : array
	{
		return [
			'with POST and the field types match and the value is set' => [[
				'data' => [
					'attributes' => [
						'field_type' => 'number',
					],
				],
				'method' => 'POST',
				'allowedFieldType' => 'number',
				'actionType' => [],
				'value' => 'bar',
				'expected' => true,
			]],
			'with POST and the field types do not match and the value is set' => [[
				'data' => [
					'attributes' => [
						'field_type' => 'number',
					],
				],
				'method' => 'POST',
				'allowedFieldType' => 'button',
				'actionType' => [],
				'value' => 'bar',
				'expected' => false,
			]],
			'with POST and the field types match and the value is not set' => [[
				'data' => [
					'attributes' => [
						'field_type' => 'number',
					],
				],
				'method' => 'POST',
				'allowedFieldType' => 'number',
				'actionType' => [],
				'value' => '',
				'expected' => true,
			]],
			'with POST and the field types do not match and the value is not set' => [[
				'data' => [
					'attributes' => [
						'field_type' => 'number',
					],
				],
				'method' => 'POST',
				'allowedFieldType' => 'button',
				'actionType' => [],
				'value' => '',
				'expected' => true,
			]],

			'with PUT and the field types match and the value is set' => [[
				'data' => [],
				'method' => 'PUT',
				'allowedFieldType' => 'number',
				'actionType' => ['field_type' => 'number'],
				'value' => 'bar',
				'expected' => true,
			]],
			'with PUT and the field types do not match and the value is set' => [[
				'data' => [],
				'method' => 'PUT',
				'allowedFieldType' => 'button',
				'actionType' => ['field_type' => 'number'],
				'value' => 'bar',
				'expected' => false,
			]],
			'with PUT and the field types match and the value is not set' => [[
				'data' => [],
				'method' => 'PUT',
				'allowedFieldType' => 'number',
				'actionType' => ['field_type' => 'number'],
				'value' => '',
				'expected' => true,
			]],
			'with PUT and the field types do not match and the value is not set' => [[
				'data' => [],
				'method' => 'PUT',
				'allowedFieldType' => 'button',
				'actionType' => ['field_type' => 'number'],
				'value' => '',
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
		$rule = new OnlyIfFieldType($args['data'], $args['method'], $args['allowedFieldType'], $args['actionType']);
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
