<?php

namespace Tests\Unit\Rules;

use App\Rules\TempIdsOnly;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TempIdsOnlyTest extends TestCase
{
	public static function passesProvider() : array
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

	#[DataProvider('passesProvider')]
	public function testPasses(array $args) : void
	{
		$rule = new TempIdsOnly();
		$output = $rule->passes('foo', ['data' => $args['value']]);
		$this->assertSame($args['expected'], $output);
	}
}
