<?php

namespace Tests\Unit\Rules;

use App\Rules\NotPresent;
use Tests\TestCase;

class NotPresentTest extends TestCase
{
	public function passesProvider()
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

	/**
	 * @dataProvider passesProvider
	 */
	public function testPasses($args)
	{
		$rule = new NotPresent();
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
