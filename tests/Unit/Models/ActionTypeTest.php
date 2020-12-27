<?php

namespace Tests\Unit\Models;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionTypeTest extends TestCase
{
	use RefreshDatabase;

	public function testGetInProgressAttribute()
	{
		// Non-continuous action type.
		$actionType = ActionType::factory()->create(['is_continuous' => false]);
		$this->assertSame(null, $actionType->inProgress);

		// Continuous action type with no actions.
		$actionType->is_continuous = true;
		$actionType->save();
		$this->assertSame(null, $actionType->inProgress);

		// Continuous action type with no in-progress actions.
		$option = Option::factory()->create(['action_type_id' => $actionType->id]);
		$action = Action::factory()->create([
			'action_type_id' => $actionType->id,
			'start_date' => '2001-02-03 04:05:06',
			'end_date' => '2001-02-03 05:05:06',
			'option_id' => $option->id,
		]);
		$this->assertSame(null, $actionType->inProgress);

		// Continuous action type with in-progress action.
		$action->end_date = null;
		$action->save();
		$this->assertSame([
			'id' => (string) $action->id,
			'start_date' => '2001-02-03 04:05:06',
			'option' => [
				'id' => (string) $option->id,
				'type' => 'options',
			],
		], $actionType->inProgress);
	}

	public function testGetSlugAttribute()
	{
		$actionType = ActionType::factory()->make(['label' => 'Foo Bar']);
		$this->assertSame('foo-bar', $actionType->slug);
	}
}
