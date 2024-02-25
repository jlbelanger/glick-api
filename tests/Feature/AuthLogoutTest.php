<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLogoutTest extends TestCase
{
	use RefreshDatabase;

	protected $user;

	protected $token;

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create();
		$this->token = $this->user->createToken('api')->plainTextToken;
	}

	public function testValidLogout() : void
	{
		$response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])->json('DELETE', '/auth/logout');
		$response->assertNoContent(204);

		$response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])->json('DELETE', '/auth/logout');
		$response->assertNoContent(204);
	}

	public function testInvalidLogout() : void
	{
		$response = $this->json('DELETE', '/auth/logout');
		$response->assertExactJson([
			'errors' => [
				[
					'title' => 'You are not logged in.',
					'status' => '401',
				],
			],
		]);
		$response->assertStatus(401);
	}
}
