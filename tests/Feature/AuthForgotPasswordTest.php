<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthForgotPasswordTest extends TestCase
{
	use RefreshDatabase;

	protected $user;

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create();
	}

	public static function forgotPasswordProvider() : array
	{
		return [
			'with missing email' => [[
				'body' => [
					'data' => [
						'attributes' => [],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The email field is required.',
							'status' => '422',
							'source' => [
								'pointer' => '/data/attributes/email',
							],
						],
					],
				],
				'code' => 422,
				'sent' => false,
			]],
			'with invalid email' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'email' => 'invalid',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The email must be a valid email address.',
							'status' => '422',
							'source' => [
								'pointer' => '/data/attributes/email',
							],
						],
					],
				],
				'code' => 422,
				'sent' => false,
			]],
			'with non-existent email' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'email' => 'doesnotexist@example.com',
						],
					],
				],
				'response' => null,
				'code' => 204,
				'sent' => false,
			]],
			'with valid email' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'email' => 'foo@example.com',
						],
					],
				],
				'response' => null,
				'code' => 204,
				'sent' => true,
			]],
		];
	}

	#[DataProvider('forgotPasswordProvider')]
	public function testForgotPassword(array $args) : void
	{
		Notification::fake();

		$response = $this->json('POST', '/auth/forgot-password', $args['body']);
		if (!empty($args['response'])) {
			$response->assertExactJson($args['response']);
			$response->assertStatus($args['code']);
		} else {
			$response->assertNoContent($args['code']);
		}
		if (!empty($args['sent'])) {
			Notification::assertSentTo($this->user, ResetPassword::class);
		} else {
			Notification::assertNothingSent();
		}
	}
}
