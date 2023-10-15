<?php

namespace Tests\Unit\Rules;

use App\Models\Action;
use App\Rules\ActionOptionForNonButton;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionOptionForNonButtonTest extends TestCase
{
	use RefreshDatabase;

	public function passesProvider() : array
	{
		return [];
	}

	/**
	 * @dataProvider passesProvider
	 */
	public function testPasses(array $args) : void
	{
		$this->markAsSkipped();
		$args['action'] = Action::factory()->create($args['action']);
		$rule = new ActionOptionForNonButton($args['action'], $args['data']);
		$output = $rule->passes('foo', $args['value']);
		$this->assertSame($args['expected'], $output);
	}
}
