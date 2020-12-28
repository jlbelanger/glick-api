<?php

namespace Tests\Unit\Rules;

use App\Models\ActionType;
use App\Rules\CannotRemoveWithEvents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CannotRemoveWithEventsTest extends TestCase
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
		$args['actionType'] = ActionType::factory()->create($args['actionType']);
		$rule = new CannotRemoveWithEvents($args['actionType']);
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
