<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLogoutTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create();
		$this->token = $this->user->createToken('api')->plainTextToken;
	}

	public function testLogout() : void
	{
		$response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])->json('DELETE', '/auth/logout');
		$response->assertNoContent(204);
	}
}
