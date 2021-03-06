<?php

namespace Tests\Unit\Rules;

use App\Models\Action;
use App\Rules\ActionOptionForButton;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionOptionForButtonTest extends TestCase
{
	use RefreshDatabase;

	public function passesProvider()
	{
		return [];
	}

	/**
	 * @dataProvider passesProvider
	 */
	public function testPasses($args)
	{
		$this->markAsSkipped();
		$args['action'] = Action::factory()->create($args['action']);
		$rule = new ActionOptionForButton($args['action'], $args['data']);
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
