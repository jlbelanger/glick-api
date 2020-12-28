<?php

namespace Tests\Unit\Rules;

use App\Rules\CannotChange;
use Tests\TestCase;

class CannotChangeTest extends TestCase
{
	public function passesProvider()
	{
		return [
			[[
				'value' => 'bar',
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

	/**
	 * @dataProvider passesProvider
	 */
	public function testPasses($args)
	{
		$rule = new CannotChange();
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
