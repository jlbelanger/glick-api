<?php

namespace Tests\Unit\Observers;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionObserverTest extends TestCase
{
	use RefreshDatabase;

	public function testCreating() : void
	{
		$actionType = ActionType::factory()->create(['is_continuous' => true]);

		// First action end date is not affected.
		$action1 = Action::factory()->create(['action_type_id' => $actionType->id]);
		$this->assertSame(null, $action1->fresh()->end_date);

		// Creating a second action ends the first action.
		$action2 = Action::factory()->create(['action_type_id' => $actionType->id]);
		$this->assertSame(null, $action2->fresh()->end_date);
		$this->assertMatchesRegularExpression('/^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d$/', $action1->fresh()->end_date);
	}
}
