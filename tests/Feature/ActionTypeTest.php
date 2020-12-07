<?php

namespace Tests\Feature;

use App\Models\ActionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionTypeTest extends TestCase
{
	use RefreshDatabase;

	protected $path = '/action-types';

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create([]);
		$this->otherUser = User::factory()->create(['email' => 'bar@example.com', 'username' => 'bar']);
		$this->actionType = ActionType::factory()->create(['user_id' => $this->user->id]);
		$this->actionTypeNumber = ActionType::factory()->create(['user_id' => $this->user->id, 'field_type' => 'number']);
		$this->actionTypeOtherUser = ActionType::factory()->create(['user_id' => $this->otherUser->id]);
	}

	public function testIndex()
	{
		$response = $this->actingAs($this->user)->json('GET', $this->path);
		$response->assertExactJson([
			'data' => [
				[
					'id' => (string) $this->actionType->id,
					'type' => 'action-types',
					'attributes' => [
						'label' => 'Foo',
						'is_continuous' => 0,
						'field_type' => 'button',
						'suffix' => null,
						'options' => null,
						'order_num' => 0,
						'in_progress' => null,
						'slug' => 'foo',
					],
				],
				[
					'id' => (string) $this->actionTypeNumber->id,
					'type' => 'action-types',
					'attributes' => [
						'label' => 'Foo',
						'is_continuous' => 0,
						'field_type' => 'number',
						'suffix' => null,
						'options' => null,
						'order_num' => 0,
						'in_progress' => null,
						'slug' => 'foo',
					],
				],
			],
		])
		->assertStatus(200);
	}

	public function storeProvider()
	{
		return [
			'with missing required fields' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label field is required.',
							'source' => [
								'pointer' => '/data/attributes/label',
							],
							'status' => '422',
						],
						[
							'title' => 'The field type field is required.',
							'source' => [
								'pointer' => '/data/attributes/field_type',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with too long label' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => str_pad('', 256, 'a', STR_PAD_LEFT),
							'field_type' => 'button',
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label may not be greater than 255 characters.',
							'source' => [
								'pointer' => '/data/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with suffix for button' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
							'suffix' => 'foo',
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The suffix cannot be set unless the field type is "number".',
							'source' => [
								'pointer' => '/data/attributes/suffix',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with too long suffix' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'number',
							'suffix' => str_pad('', 256, 'a', STR_PAD_LEFT),
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The suffix may not be greater than 255 characters.',
							'source' => [
								'pointer' => '/data/attributes/suffix',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with a non-integer order_num' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
							'order_num' => 'foo',
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The order num must be an integer.',
							'source' => [
								'pointer' => '/data/attributes/order_num',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with invalid field_type' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'foo',
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The selected field type is invalid.',
							'source' => [
								'pointer' => '/data/attributes/field_type',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with is_continuous for number' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'number',
							'is_continuous' => true,
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The is continuous cannot be set unless the field type is "button".',
							'source' => [
								'pointer' => '/data/attributes/is_continuous',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with non-boolean is_continuous' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
							'is_continuous' => 'foo',
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The is continuous field must be true or false.',
							'source' => [
								'pointer' => '/data/attributes/is_continuous',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with user' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
						],
						'relationships' => [
							'user' => [
								'data' => [
									'id' => '1',
									'type' => 'users',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The user field cannot be present.',
							'source' => [
								'pointer' => '/data/relationships/user',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with minimal valid attributes for button' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
						],
					],
				],
				'params' => '?include=user',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'button',
							'suffix' => null,
							'options' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
						],
						'relationships' => [
							'user' => [
								'data' => [
									'id' => '%user_id%',
									'type' => 'users',
								],
							],
						],
					],
					'included' => [
						[
							'id' => '%user_id%',
							'type' => 'users',
							'attributes' => [
								'username' => 'foo',
								'email' => 'foo@example.com',
							],
						],
					],
				],
				'code' => 201,
			]],
			'with all valid attributes for button' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
							'is_continuous' => true,
							'order_num' => 1,
						],
					],
				],
				'params' => '?include=user',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 1,
							'field_type' => 'button',
							'suffix' => null,
							'options' => null,
							'order_num' => 1,
							'in_progress' => null,
							'slug' => 'foo',
						],
						'relationships' => [
							'user' => [
								'data' => [
									'id' => '%user_id%',
									'type' => 'users',
								],
							],
						],
					],
					'included' => [
						[
							'id' => '%user_id%',
							'type' => 'users',
							'attributes' => [
								'username' => 'foo',
								'email' => 'foo@example.com',
							],
						],
					],
				],
				'code' => 201,
			]],
			'with minimal valid attributes for number' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'number',
						],
					],
				],
				'params' => '?include=user',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'number',
							'suffix' => null,
							'options' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
						],
						'relationships' => [
							'user' => [
								'data' => [
									'id' => '%user_id%',
									'type' => 'users',
								],
							],
						],
					],
					'included' => [
						[
							'id' => '%user_id%',
							'type' => 'users',
							'attributes' => [
								'username' => 'foo',
								'email' => 'foo@example.com',
							],
						],
					],
				],
				'code' => 201,
			]],
			'with all valid attributes for number' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'number',
							'suffix' => 'bar',
							'order_num' => 1,
						],
					],
				],
				'params' => '?include=user',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'number',
							'suffix' => 'bar',
							'options' => null,
							'order_num' => 1,
							'in_progress' => null,
							'slug' => 'foo',
						],
						'relationships' => [
							'user' => [
								'data' => [
									'id' => '%user_id%',
									'type' => 'users',
								],
							],
						],
					],
					'included' => [
						[
							'id' => '%user_id%',
							'type' => 'users',
							'attributes' => [
								'username' => 'foo',
								'email' => 'foo@example.com',
							],
						],
					],
				],
				'code' => 201,
			]],
		];
	}

	/**
	 * @dataProvider storeProvider
	 */
	public function testStore($args)
	{
		$response = $this->actingAs($this->user)->json('POST', $this->path . $args['params'], $args['body']);
		$args['response'] = $this->replaceToken('%user_id%', $this->user->id, $args['response']);
		if (!empty($response['data']['id'])) {
			$args['response'] = $this->replaceToken('%id%', $response['data']['id'], $args['response']);
		}
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public function showProvider()
	{
		return [
			"with another user's record" => [[
				'key' => 'actionTypeOtherUser',
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
			"with current user's record" => [[
				'key' => 'actionType',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'button',
							'suffix' => null,
							'options' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
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
		$args['response'] = $this->replaceToken('%id%', $this->actionType->id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->{$args['key']}->id);
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public function updateProvider()
	{
		return [
			"with another user's record" => [[
				'key' => 'actionTypeOtherUser',
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
			'with too long label' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => str_pad('', 256, 'a', STR_PAD_LEFT),
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The label may not be greater than 255 characters.',
							'source' => [
								'pointer' => '/data/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with a null label' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => null,
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The label field must have a value.',
							'source' => [
								'pointer' => '/data/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when setting a suffix for a button' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'suffix' => 'foo',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The suffix cannot be set unless the field type is "number".',
							'source' => [
								'pointer' => '/data/attributes/suffix',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with too long suffix' => [[
				'key' => 'actionTypeNumber',
				'body' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'attributes' => [
							'suffix' => str_pad('', 256, 'a', STR_PAD_LEFT),
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The suffix may not be greater than 255 characters.',
							'source' => [
								'pointer' => '/data/attributes/suffix',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with a non-integer order_num' => [[
				'key' => 'actionTypeNumber',
				'body' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'attributes' => [
							'order_num' => 'foo',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The order num must be an integer.',
							'source' => [
								'pointer' => '/data/attributes/order_num',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when changing field_type' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'field_type' => 'number',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The field type cannot be changed.',
							'source' => [
								'pointer' => '/data/attributes/field_type',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when changing is_continuous' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'is_continuous' => true,
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The is continuous cannot be changed.',
							'source' => [
								'pointer' => '/data/attributes/is_continuous',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when changing user' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'relationships' => [
							'user' => [
								'data' => [
									'id' => '1',
									'type' => 'users',
								],
							],
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The user cannot be changed.',
							'source' => [
								'pointer' => '/data/relationships/user',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with no attributes' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
					],
				],
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'button',
							'suffix' => null,
							'options' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
						],
					],
				],
				'code' => 200,
			]],
			'with valid label' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Bar',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Bar',
							'is_continuous' => 0,
							'field_type' => 'button',
							'suffix' => null,
							'options' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'bar',
						],
					],
				],
				'code' => 200,
			]],
			'with valid suffix' => [[
				'key' => 'actionTypeNumber',
				'body' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'attributes' => [
							'suffix' => 'bar',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'number',
							'suffix' => 'bar',
							'options' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
						],
					],
				],
				'code' => 200,
			]],
			'with null suffix' => [[
				'key' => 'actionTypeNumber',
				'body' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'attributes' => [
							'suffix' => null,
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'number',
							'suffix' => null,
							'options' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
						],
					],
				],
				'code' => 200,
			]],
			'with valid order_num' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'order_num' => 1,
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'button',
							'suffix' => null,
							'options' => null,
							'order_num' => 1,
							'in_progress' => null,
							'slug' => 'foo',
						],
					],
				],
				'code' => 200,
			]],
			'with 0 order_num' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'order_num' => 0,
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => 0,
							'field_type' => 'button',
							'suffix' => null,
							'options' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
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
		$tokens = [
			'%actionType.id%' => $this->actionType->id,
			'%actionTypeNumber.id%' => $this->actionTypeNumber->id,
		];
		$args['body'] = $this->replaceTokens($tokens, $args['body']);
		$args['response'] = $this->replaceTokens($tokens, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->{$args['key']}->id, $args['body']);
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public function destroyProvider()
	{
		return [
			"with another user's record" => [[
				'key' => 'actionTypeOtherUser',
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
			"with current user's record" => [[
				'key' => 'actionType',
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
