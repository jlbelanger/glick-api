<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\Option;
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
		$this->actionTypeOptions = ActionType::factory()->create(['user_id' => $this->user->id]);
		$this->actionTypeNumber = ActionType::factory()->create(['user_id' => $this->user->id, 'field_type' => 'number']);
		$this->actionTypeOtherUser = ActionType::factory()->create(['user_id' => $this->otherUser->id]);
		$this->optionA = Option::factory()->create(['action_type_id' => $this->actionTypeOptions->id, 'label' => 'A']);
		$this->optionB = Option::factory()->create(['action_type_id' => $this->actionTypeOptions->id, 'label' => 'B']);
		$this->action = Action::factory()->create(['action_type_id' => $this->actionTypeOptions->id, 'option_id' => $this->optionB]);
	}

	public function testIndex() : void
	{
		$response = $this->actingAs($this->user)->json('GET', $this->path);
		$response->assertExactJson([
			'data' => [
				[
					'id' => (string) $this->actionType->id,
					'type' => 'action-types',
					'attributes' => [
						'label' => 'Foo',
						'is_continuous' => false,
						'field_type' => 'button',
						'suffix' => null,
						'order_num' => 0,
						'in_progress' => null,
						'slug' => 'foo',
						'is_archived' => false,
					],
				],
				[
					'id' => (string) $this->actionTypeOptions->id,
					'type' => 'action-types',
					'attributes' => [
						'label' => 'Foo',
						'is_continuous' => false,
						'field_type' => 'button',
						'suffix' => null,
						'order_num' => 0,
						'in_progress' => null,
						'slug' => 'foo',
						'is_archived' => false,
					],
				],
				[
					'id' => (string) $this->actionTypeNumber->id,
					'type' => 'action-types',
					'attributes' => [
						'label' => 'Foo',
						'is_continuous' => false,
						'field_type' => 'number',
						'suffix' => null,
						'order_num' => 0,
						'in_progress' => null,
						'slug' => 'foo',
						'is_archived' => false,
					],
				],
			],
		])
		->assertStatus(200);
	}

	public static function storeProvider() : array
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
							'title' => 'The label must not be greater than 255 characters.',
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
							'title' => 'The suffix field is prohibited.',
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
							'title' => 'The suffix must not be greater than 255 characters.',
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
			'with options for number' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'number',
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => 'Bar',
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The options field is prohibited.',
							'source' => [
								'pointer' => '/data/relationships/options',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with missing option label' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label field is required.',
							'source' => [
								'pointer' => '/included/0/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with empty string option label' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => '',
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label field is required.',
							'source' => [
								'pointer' => '/included/0/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with null option label' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => null,
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label field is required.',
							'source' => [
								'pointer' => '/included/0/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with too long option label' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => str_pad('', 256, 'a', STR_PAD_LEFT),
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label must not be greater than 255 characters.',
							'source' => [
								'pointer' => '/included/0/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with existing/non-temp option IDs' => [[
				'body' => [
					'data' => [
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'field_type' => 'button',
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => '%optionA.id%',
										'type' => 'options',
									],
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The options cannot contain existing options.',
							'source' => [
								'pointer' => '/data/relationships/options',
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
							'title' => 'The is continuous field is prohibited.',
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
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
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
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => 'Bar',
								'action_type_id' => 'temp-this-id',
							],
						],
					],
				],
				'params' => '?include=options,user',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => true,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 1,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => '%option_id%',
										'type' => 'options',
									],
								],
							],
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
							'id' => '%option_id%',
							'type' => 'options',
							'attributes' => [
								'label' => 'Bar',
								'has_events' => false,
							],
						],
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
							'is_continuous' => false,
							'field_type' => 'number',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
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
							'is_continuous' => false,
							'field_type' => 'number',
							'suffix' => 'bar',
							'order_num' => 1,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
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
	public function testStore(array $args) : void
	{
		$args['body'] = $this->replaceToken('%optionA.id%', (string) $this->optionA->id, $args['body']);
		$response = $this->actingAs($this->user)->json('POST', $this->path . $args['params'], $args['body']);
		$args['response'] = $this->replaceToken('%user_id%', (string) $this->user->id, $args['response']);
		if (!empty($response['data']['id'])) {
			$args['response'] = $this->replaceToken('%id%', $response['data']['id'], $args['response']);
		}
		if (!empty($response['data']['relationships']['options']['data'][0]['id'])) {
			$args['response'] = $this->replaceToken('%option_id%', $response['data']['relationships']['options']['data'][0]['id'], $args['response']);
		}
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public static function showProvider() : array
	{
		return [
			"with another user's record" => [[
				'key' => 'actionTypeOtherUser',
				'response' => [
					'errors' => [
						[
							'status' => '404',
							'title' => 'This record does not exist.',
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
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
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
	public function testShow(array $args) : void
	{
		$args['response'] = $this->replaceToken('%id%', (string) $this->actionType->id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->{$args['key']}->id);
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public static function updateProvider() : array
	{
		return [
			"with another user's record" => [[
				'key' => 'actionTypeOtherUser',
				'body' => [],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'status' => '404',
							'title' => 'This record does not exist.',
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
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label must not be greater than 255 characters.',
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
				'params' => '',
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
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The suffix field is prohibited.',
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
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The suffix must not be greater than 255 characters.',
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
			'with options for number' => [[
				'key' => 'actionTypeNumber',
				'body' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => 'Bar',
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The options field is prohibited.',
							'source' => [
								'pointer' => '/data/relationships/options',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with missing option label' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label field is required.',
							'source' => [
								'pointer' => '/included/0/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with empty string option label' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => '',
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label field is required.',
							'source' => [
								'pointer' => '/included/0/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with empty string option label' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => null,
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label field is required.',
							'source' => [
								'pointer' => '/included/0/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with too long option label' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => str_pad('', 256, 'a', STR_PAD_LEFT),
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The label must not be greater than 255 characters.',
							'source' => [
								'pointer' => '/included/0/attributes/label',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when removing an option that has events' => [[
				'key' => 'actionTypeOptions',
				'body' => [
					'data' => [
						'id' => '%actionTypeOptions.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => '%optionA.id%',
										'type' => 'options',
									],
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'Options with existing events cannot be removed.',
							'source' => [
								'pointer' => '/data/relationships/options',
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
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The field type field is prohibited.',
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
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The is continuous field is prohibited.',
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
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The user field is prohibited.',
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
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
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
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Bar',
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'bar',
							'is_archived' => false,
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
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => false,
							'field_type' => 'number',
							'suffix' => 'bar',
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
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
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionTypeNumber.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => false,
							'field_type' => 'number',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
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
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 1,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
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
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
						],
					],
				],
				'code' => 200,
			]],
			'when adding an option' => [[
				'key' => 'actionType',
				'body' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => 'temp-1',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => 'temp-1',
							'type' => 'options',
							'attributes' => [
								'label' => 'Bar',
								'action_type_id' => 'temp-this-id',
							],
						],
					],
				],
				'params' => '?include=options',
				'response' => [
					'data' => [
						'id' => '%actionType.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => '%option.id%',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => '%option.id%',
							'type' => 'options',
							'attributes' => [
								'label' => 'Bar',
								'has_events' => false,
							],
						],
					],
				],
				'code' => 200,
			]],
			'when renaming an option' => [[
				'key' => 'actionTypeOptions',
				'body' => [
					'data' => [
						'id' => '%actionTypeOptions.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => '%optionA.id%',
										'type' => 'options',
									],
									[
										'id' => '%optionB.id%',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => '%optionA.id%',
							'type' => 'options',
							'attributes' => [
								'label' => 'New Label',
							],
						],
					],
				],
				'params' => '?include=options',
				'response' => [
					'data' => [
						'id' => '%actionTypeOptions.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => '%optionA.id%',
										'type' => 'options',
									],
									[
										'id' => '%optionB.id%',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => '%optionA.id%',
							'type' => 'options',
							'attributes' => [
								'label' => 'New Label',
								'has_events' => false,
							],
						],
						[
							'id' => '%optionB.id%',
							'type' => 'options',
							'attributes' => [
								'label' => 'B',
								'has_events' => true,
							],
						],
					],
				],
				'code' => 200,
			]],
			'when removing an option that has no events' => [[
				'key' => 'actionTypeOptions',
				'body' => [
					'data' => [
						'id' => '%actionTypeOptions.id%',
						'type' => 'action-types',
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => '%optionB.id%',
										'type' => 'options',
									],
								],
							],
						],
					],
				],
				'params' => '?include=options',
				'response' => [
					'data' => [
						'id' => '%actionTypeOptions.id%',
						'type' => 'action-types',
						'attributes' => [
							'label' => 'Foo',
							'is_continuous' => false,
							'field_type' => 'button',
							'suffix' => null,
							'order_num' => 0,
							'in_progress' => null,
							'slug' => 'foo',
							'is_archived' => false,
						],
						'relationships' => [
							'options' => [
								'data' => [
									[
										'id' => '%optionB.id%',
										'type' => 'options',
									],
								],
							],
						],
					],
					'included' => [
						[
							'id' => '%optionB.id%',
							'type' => 'options',
							'attributes' => [
								'label' => 'B',
								'has_events' => true,
							],
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
	public function testUpdate(array $args) : void
	{
		$tokens = [
			'%actionType.id%' => (string) $this->actionType->id,
			'%actionTypeNumber.id%' => (string) $this->actionTypeNumber->id,
			'%actionTypeOptions.id%' => (string) $this->actionTypeOptions->id,
			'%optionA.id%' => (string) $this->optionA->id,
			'%optionB.id%' => (string) $this->optionB->id,
		];
		$args['body'] = $this->replaceTokens($tokens, $args['body']);
		$args['response'] = $this->replaceTokens($tokens, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->{$args['key']}->id . $args['params'], $args['body']);
		if (!empty($response['data']['relationships']['options']['data'][0]['id'])) {
			$args['response'] = $this->replaceToken('%option.id%', $response['data']['relationships']['options']['data'][0]['id'], $args['response']);
		}
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public static function destroyProvider() : array
	{
		return [
			"with another user's record" => [[
				'key' => 'actionTypeOtherUser',
				'response' => [
					'errors' => [
						[
							'status' => '404',
							'title' => 'This record does not exist.',
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
	public function testDestroy(array $args) : void
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
