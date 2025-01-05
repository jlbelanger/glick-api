<?php

namespace Tests\Unit\Policies;

use App\Models\ActionType;
use App\Models\User;
use App\Policies\ActionTypePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionTypePolicyTest extends TestCase
{
	use RefreshDatabase;

	protected $policy;

	protected $user;

	protected $otherUser;

	protected $actionTypeOwned;

	protected $actionTypeNotOwned;

	protected function setUp() : void
	{
		parent::setUp();
		$this->policy = new ActionTypePolicy();
		$this->user = User::factory()->create([]);
		$this->otherUser = User::factory()->create(['email' => 'bar@example.com', 'username' => 'bar']);
		$this->actionTypeOwned = ActionType::factory()->create(['user_id' => $this->user->id]);
		$this->actionTypeNotOwned = ActionType::factory()->create(['user_id' => $this->otherUser->id]);
	}

	public function testView() : void
	{
		$this->assertSame(true, $this->policy->view($this->user, $this->actionTypeOwned));
		$this->assertSame(false, $this->policy->view($this->user, $this->actionTypeNotOwned));
	}

	public function testCreate() : void
	{
		$this->assertSame(true, $this->policy->create($this->user, $this->actionTypeOwned));
		$this->assertSame(true, $this->policy->create($this->user, $this->actionTypeNotOwned));
	}

	public function testDelete() : void
	{
		$this->assertSame(true, $this->policy->delete($this->user, $this->actionTypeOwned));
		$this->assertSame(false, $this->policy->delete($this->user, $this->actionTypeNotOwned));
	}

	public function testUpdate() : void
	{
		$this->assertSame(true, $this->policy->update($this->user, $this->actionTypeOwned));
		$this->assertSame(false, $this->policy->update($this->user, $this->actionTypeNotOwned));
	}

	public function testViewAny() : void
	{
		$this->assertSame(true, $this->policy->viewAny($this->user, $this->actionTypeOwned));
		$this->assertSame(true, $this->policy->viewAny($this->user, $this->actionTypeNotOwned));
	}
}
