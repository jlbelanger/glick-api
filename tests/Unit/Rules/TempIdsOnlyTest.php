<?php

namespace Tests\Unit\Rules;

use App\Rules\TempIdsOnly;
use Tests\TestCase;

class TempIdsOnlyTest extends TestCase
{
	public function passesProvider()
	{
		return [
			[[
				'value' => [],
				'expected' => true,
			]],
			[[
				'value' => [
					['id' => 'temp-1'],
				],
				'expected' => true,
			]],
			[[
				'value' => [
					['id' => 'temp-1'],
					['id' => 'temp-2'],
					['id' => 'temp-3'],
				],
				'expected' => true,
			]],
			[[
				'value' => [
					['id' => '1'],
				],
				'expected' => false,
			]],
			[[
				'value' => [
					['id' => 'temp-1'],
					['id' => 'temp-2'],
					['id' => '1'],
				],
				'expected' => false,
			]],
		];
	}

	/**
	 * @dataProvider passesProvider
	 */
	public function testPasses($args)
	{
		$rule = new TempIdsOnly();
		$output = $rule->passes('foo', ['data' => $args['value']]);
		$this->assertSame($args['expected'], $output);
	}
}
