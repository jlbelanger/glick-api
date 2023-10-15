<?php

namespace Tests\Feature;

use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthResetPasswordTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create();
		$this->token = 'b77e25ef5a0df870db09150c4913f9e2139a70aa751e4f5dc55f91c298b34447';
		DB::table('password_resets')->insert(['email' => $this->user->email, 'token' => Hash::make($this->token)]);
	}

	public function resetPasswordProvider() : array
	{
		return [
			'with missing fields' => [[
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
						[
							'title' => 'The new password field is required.',
							'status' => '422',
							'source' => [
								'pointer' => '/data/attributes/new_password',
							],
						],
					],
				],
				'code' => 422,
			]],
			'with invalid email' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'email' => 'invalid',
							'new_password' => 'password2',
							'new_password_confirmation' => 'password2',
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
			]],
		];
	}

	/**
	 * @dataProvider resetPasswordProvider
	 */
	public function testResetPassword(array $args) : void
	{
		$url = URL::temporarySignedRoute(
			'password.update',
			Carbon::now()->addMinutes(60),
			['token' => $this->token],
			false
		);
		$response = $this->json('PUT', $url, $args['body']);
		if (!empty($args['response'])) {
			$response->assertExactJson($args['response']);
			$response->assertStatus($args['code']);
		} else {
			$response->assertNoContent($args['code']);
		}
	}

	public function testPasswordCanBeResetWithValidToken() : void
	{
		$url = URL::temporarySignedRoute(
			'password.update',
			Carbon::now()->addMinutes(60),
			['token' => $this->token],
			false
		);

		$response = $this->json('PUT', $url, [
			'data' => [
				'attributes' => [
					'email' => $this->user->email,
					'new_password' => 'password2',
					'new_password_confirmation' => 'password2',
				],
			],
		]);

		$response->assertNoContent(204);
	}

	public function testPasswordCannotBeResetTwiceWithSameToken() : void
	{
		$url = URL::temporarySignedRoute(
			'password.update',
			Carbon::now()->addMinutes(60),
			['token' => $this->token],
			false
		);

		$response = $this->json('PUT', $url, [
			'data' => [
				'attributes' => [
					'email' => $this->user->email,
					'new_password' => 'password2',
					'new_password_confirmation' => 'password2',
				],
			],
		]);

		$response->assertNoContent(204);

		$response = $this->json('PUT', $url, [
			'data' => [
				'attributes' => [
					'email' => $this->user->email,
					'new_password' => 'password3',
					'new_password_confirmation' => 'password3',
				],
			],
		]);

		$response->assertExactJson([
			'errors' => [
				[
					'title' => 'This password reset link is invalid or the email is incorrect.',
					'status' => '403',
				],
			],
		]);
		$response->assertStatus(403);
	}

	public function testPasswordCannotBeResetWithWrongEmail() : void
	{
		$url = URL::temporarySignedRoute(
			'password.update',
			Carbon::now()->addMinutes(60),
			['token' => $this->token],
			false
		);

		$response = $this->json('PUT', $url, [
			'data' => [
				'attributes' => [
					'email' => 'wrongemail@example.com',
					'new_password' => 'password2',
					'new_password_confirmation' => 'password2',
				],
			],
		]);

		$response->assertExactJson([
			'errors' => [
				[
					'title' => 'This password reset link is invalid or the email is incorrect.',
					'status' => '403',
				],
			],
		]);
		$response->assertStatus(403);
	}

	public function testPasswordCannotBeResetWithExpiredToken() : void
	{
		$url = URL::temporarySignedRoute(
			'password.update',
			Carbon::now()->subMinutes(60),
			['token' => $this->token],
			false
		);

		$response = $this->json('PUT', $url, [
			'data' => [
				'attributes' => [
					'email' => $this->user->email,
					'new_password' => 'password2',
					'new_password_confirmation' => 'password2',
				],
			],
		]);

		$response->assertExactJson([
			'errors' => [
				[
					'title' => 'This link has expired.',
					'status' => '403',
				],
			],
		]);
		$response->assertStatus(403);
	}

	public function testPasswordCannotBeResetWithInvalidToken() : void
	{
		$url = URL::temporarySignedRoute(
			'password.update',
			Carbon::now()->addMinutes(60),
			['token' => $this->token],
			false
		);
		$url = str_replace('?', 'a?', $url);

		$response = $this->json('PUT', $url, [
			'data' => [
				'attributes' => [
					'email' => $this->user->email,
					'new_password' => 'password2',
					'new_password_confirmation' => 'password2',
				],
			],
		]);

		$response->assertExactJson([
			'errors' => [
				[
					'title' => 'This password reset link is invalid or the email is incorrect.',
					'status' => '403',
				],
			],
		]);
		$response->assertStatus(403);
	}
}
