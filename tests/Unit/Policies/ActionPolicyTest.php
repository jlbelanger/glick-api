<?php

namespace Tests\Unit\Policies;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\User;
use App\Policies\ActionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionPolicyTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp() : void
	{
		parent::setUp();
		$this->policy = new ActionPolicy();
		$this->user = User::factory()->create([]);
		$this->otherUser = User::factory()->create(['email' => 'bar@example.com', 'username' => 'bar']);
		$this->actionTypeOwned = ActionType::factory()->create(['user_id' => $this->user->id]);
		$this->actionTypeNotOwned = ActionType::factory()->create(['user_id' => $this->otherUser->id]);
		$this->actionOwned = Action::factory()->create(['action_type_id' => $this->actionTypeOwned->id]);
		$this->actionNotOwned = Action::factory()->create(['action_type_id' => $this->actionTypeNotOwned->id]);
	}

	public function testView()
	{
		$this->assertSame(true, $this->policy->view($this->user, $this->actionOwned));
		$this->assertSame(false, $this->policy->view($this->user, $this->actionNotOwned));
	}

	public function testCreate()
	{
		$this->assertSame(true, $this->policy->create($this->user, $this->actionOwned));
		$this->assertSame(true, $this->policy->create($this->user, $this->actionNotOwned));
	}

	public function testDelete()
	{
		$this->assertSame(true, $this->policy->delete($this->user, $this->actionOwned));
		$this->assertSame(false, $this->policy->delete($this->user, $this->actionNotOwned));
	}

	public function testUpdate()
	{
		$this->assertSame(true, $this->policy->update($this->user, $this->actionOwned));
		$this->assertSame(false, $this->policy->update($this->user, $this->actionNotOwned));
	}

	public function testViewAny()
	{
		$this->assertSame(true, $this->policy->viewAny($this->user, $this->actionOwned));
		$this->assertSame(true, $this->policy->viewAny($this->user, $this->actionNotOwned));
	}
}
