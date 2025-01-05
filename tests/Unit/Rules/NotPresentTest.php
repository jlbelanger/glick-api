<?php

namespace Tests\Unit\Rules;

use App\Rules\NotPresent;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NotPresentTest extends TestCase
{
	public static function passesProvider() : array
	{
		return [
			[[
				'value' => 'a',
				'expected' => false,
			]],
			[[
				'value' => '',
				'expected' => false,
			]],
			[[
				'value' => '0',
				'expected' => false,
			]],
			[[
				'value' => null,
				'expected' => true,
			]],
		];
	}

	#[DataProvider('passesProvider')]
	public function testPasses(array $args) : void
	{
		$rule = new NotPresent();
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
