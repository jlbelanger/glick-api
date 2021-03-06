<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	protected $path = '/users';

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create([]);
		$this->otherUser = User::factory()->create(['email' => 'bar@example.com', 'username' => 'bar']);
	}

	public function testIndex()
	{
		$response = $this->actingAs($this->user)->json('GET', $this->path);
		$response->assertExactJson([
			'errors' => [
				[
					'status' => '404',
					'title' => 'URL does not exist.',
				],
			],
		])
		->assertStatus(404);
	}

	public function testStore()
	{
		$response = $this->actingAs($this->user)->json('POST', $this->path);
		$response->assertExactJson([
			'errors' => [
				[
					'status' => '404',
					'title' => 'URL does not exist.',
				],
			],
		])
		->assertStatus(404);
	}

	public function showProvider()
	{
		return [
			'with another user' => [[
				'key' => 'otherUser',
				'response' => [
					'errors' => [
						[
							'status' => '404',
							'title' => 'URL does not exist.',
						],
					],
				],
				'code' => 404,
			]],
			'with current user' => [[
				'key' => 'user',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'username' => 'foo',
							'email' => 'foo@example.com',
						],
					],
				],
				'code' => 200,
			]],
		];
	}

	/**
	 * @dataProvider showProvider
	 */
	public function testShow($args)
	{
		$args['response'] = $this->replaceToken('%id%', $this->user->id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->{$args['key']}->id);
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public function updateProvider()
	{
		return [
			'with another user' => [[
				'key' => 'otherUser',
				'body' => [],
				'response' => [
					'errors' => [
						[
							'status' => '404',
							'title' => 'URL does not exist.',
						],
					],
				],
				'code' => 404,
			]],
			'with a null username' => [[
				'key' => 'user',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'username' => null,
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The username field must have a value.',
							'source' => [
								'pointer' => '/data/attributes/username',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with too long username' => [[
				'key' => 'user',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'username' => str_pad('', 256, 'a', STR_PAD_LEFT),
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The username may not be greater than 255 characters.',
							'source' => [
								'pointer' => '/data/attributes/username',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with a username that has been taken' => [[
				'key' => 'user',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'username' => 'bar',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The username has already been taken.',
							'source' => [
								'pointer' => '/data/attributes/username',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when changing email' => [[
				'key' => 'user',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'email' => 'new@example.com',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The email cannot be changed.',
							'source' => [
								'pointer' => '/data/attributes/email',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when changing password' => [[
				'key' => 'user',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'password' => 'password',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The password cannot be changed.',
							'source' => [
								'pointer' => '/data/attributes/password',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with no attributes' => [[
				'key' => 'user',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'username' => 'foo',
							'email' => 'foo@example.com',
						],
					],
				],
				'code' => 200,
			]],
			'with valid username' => [[
				'key' => 'user',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'username' => 'new',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'users',
						'attributes' => [
							'username' => 'new',
							'email' => 'foo@example.com',
						],
					],
				],
				'code' => 200,
			]],
		];
	}

	/**
	 * @dataProvider updateProvider
	 */
	public function testUpdate($args)
	{
		$args['body'] = $this->replaceToken('%id%', $this->user->id, $args['body']);
		$args['response'] = $this->replaceToken('%id%', $this->user->id, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->{$args['key']}->id, $args['body']);
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public function destroyProvider()
	{
		return [
			'with another user' => [[
				'key' => 'otherUser',
				'response' => [
					'errors' => [
						[
							'status' => '404',
							'title' => 'URL does not exist.',
						],
					],
				],
				'code' => 404,
			]],
			'with current user' => [[
				'key' => 'user',
				'response' => null,
				'code' => 204,
			]],
		];
	}

	/**
	 * @dataProvider destroyProvider
	 */
	public function testDestroy($args)
	{
		$response = $this->actingAs($this->user)->json('DELETE', $this->path . '/' . $this->{$args['key']}->id);
		if ($args['response']) {
			$response->assertExactJson($args['response'])
				->assertStatus($args['code']);
		} else {
			$response->assertNoContent($args['code']);
		}
	}
}
