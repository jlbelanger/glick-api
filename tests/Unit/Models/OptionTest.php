<?php

namespace Tests\Unit\Models;

use App\Models\ActionType;
use App\Models\Action;
use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionTest extends TestCase
{
	use RefreshDatabase;

	public function testGetHasEventsAttributee() : void
	{
		// With no actions.
		$actionType = ActionType::factory()->create();
		$option = Option::factory()->create(['action_type_id' => $actionType->id]);
		$this->assertSame(false, $option->hasEvents);

		// With action.
		$action = Action::factory()->create([
			'action_type_id' => $actionType->id,
			'option_id' => $option->id,
		]);
		$this->assertSame(true, $option->hasEvents);
	}
}
