<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create();
	}

	public function loginProvider() : array
	{
		return [
			'with missing username' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'password' => 'password',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The username field is required.',
							'status' => '422',
							'source' => [
								'pointer' => '/data/attributes/username',
							],
						],
					],
				],
				'code' => 422,
			]],
			'with missing password' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'username' => 'foo',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The password field is required.',
							'status' => '422',
							'source' => [
								'pointer' => '/data/attributes/password',
							],
						],
					],
				],
				'code' => 422,
			]],
			'with non-string username' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'username' => 1,
							'password' => 'password',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The username must be a string.',
							'status' => '422',
							'source' => [
								'pointer' => '/data/attributes/username',
							],
						],
					],
				],
				'code' => 422,
			]],
			'with non-string password' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'username' => 'foo',
							'password' => 1,
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The password must be a string.',
							'status' => '422',
							'source' => [
								'pointer' => '/data/attributes/password',
							],
						],
					],
				],
				'code' => 422,
			]],
			'with non-boolean remember' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'username' => 'foo',
							'password' => 'password',
							'remember' => 'bar',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The remember field must be true or false.',
							'status' => '422',
							'source' => [
								'pointer' => '/data/attributes/remember',
							],
						],
					],
				],
				'code' => 422,
			]],
			'with invalid username' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'username' => 'invalid',
							'password' => 'password',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'Username or password is incorrect.',
							'status' => '401',
						],
					],
				],
				'code' => 401,
			]],
			'with invalid password' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'username' => 'foo',
							'password' => 'invalid',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'Username or password is incorrect.',
							'status' => '401',
						],
					],
				],
				'code' => 401,
			]],
			'with valid username and password' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'username' => 'foo',
							'password' => 'password',
						],
					],
				],
				'response' => [
					'token' => '%token%',
					'user' => [
						'id' => '%id%',
						'remember' => false,
					],
				],
				'code' => 200,
			]],
			'with remember' => [[
				'body' => [
					'data' => [
						'attributes' => [
							'username' => 'foo',
							'password' => 'password',
							'remember' => true,
						],
					],
				],
				'response' => [
					'token' => '%token%',
					'user' => [
						'id' => '%id%',
						'remember' => true,
					],
				],
				'code' => 200,
			]],
		];
	}

	/**
	 * @dataProvider loginProvider
	 */
	public function testLogin(array $args) : void
	{
		$response = $this->json('POST', '/auth/login', $args['body']);

		$args['response'] = $this->replaceToken('%id%', $this->user->id, $args['response']);
		if (!empty($response['token'])) {
			$args['response'] = $this->replaceToken('%token%', $response['token'], $args['response']);
		}

		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}
}
