<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
	use RefreshDatabase;

	protected $policy;

	protected $user;

	protected $otherUser;

	protected function setUp() : void
	{
		parent::setUp();
		$this->policy = new UserPolicy();
		$this->user = User::factory()->create([]);
		$this->otherUser = User::factory()->create(['email' => 'bar@example.com', 'username' => 'bar']);
	}

	public function testView() : void
	{
		$this->assertSame(true, $this->policy->view($this->user, $this->user));
		$this->assertSame(false, $this->policy->view($this->user, $this->otherUser));
	}

	public function testCreate() : void
	{
		$this->assertSame(false, $this->policy->create($this->user, $this->user));
		$this->assertSame(false, $this->policy->create($this->user, $this->otherUser));
	}

	public function testDelete() : void
	{
		$this->assertSame(true, $this->policy->delete($this->user, $this->user));
		$this->assertSame(false, $this->policy->delete($this->user, $this->otherUser));
	}

	public function testUpdate() : void
	{
		$this->assertSame(true, $this->policy->update($this->user, $this->user));
		$this->assertSame(false, $this->policy->update($this->user, $this->otherUser));
	}

	public function testViewAny() : void
	{
		$this->assertSame(false, $this->policy->viewAny($this->user, $this->user));
		$this->assertSame(false, $this->policy->viewAny($this->user, $this->otherUser));
	}
}
