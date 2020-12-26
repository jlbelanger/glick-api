<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionTest extends TestCase
{
	use RefreshDatabase;

	protected $path = '/actions';

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create([]);
		$this->otherUser = User::factory()->create(['email' => 'bar@example.com', 'username' => 'bar']);
		$this->actionType = ActionType::factory()->create(['user_id' => $this->user->id]);
		$this->actionTypeNumber = ActionType::factory()->create(['user_id' => $this->user->id, 'field_type' => 'number']);
		$this->actionTypeOtherUser = ActionType::factory()->create(['user_id' => $this->otherUser->id]);
		$this->action = Action::factory()->create(['action_type_id' => $this->actionType->id]);
		$this->actionOtherUser = Action::factory()->create(['action_type_id' => $this->actionTypeOtherUser->id]);
	}

	public function testIndex()
	{
		$response = $this->actingAs($this->user)->json('GET', $this->path);
		$response->assertExactJson([
			'data' => [
				[
					'id' => (string) $this->action->id,
					'type' => 'actions',
					'attributes' => [
						'start_date' => '2001-02-03 04:05:06',
						'end_date' => null,
						'value' => null,
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
						'type' => 'actions',
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The start date field is required.',
							'source' => [
								'pointer' => '/data/attributes/start_date',
							],
							'status' => '422',
						],
						[
							'title' => 'The action type field is required.',
							'source' => [
								'pointer' => '/data/relationships/action_type',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with invalid start_date format' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => 'foo',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionType.id%',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The start date does not match the format Y-m-d H:i:s.',
							'source' => [
								'pointer' => '/data/attributes/start_date',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with too long value' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => str_pad('', 256, 'a', STR_PAD_LEFT),
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeNumber.id%',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The value may not be greater than 255 characters.',
							'source' => [
								'pointer' => '/data/attributes/value',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with missing value for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeNumber.id%',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The value is required.',
							'source' => [
								'pointer' => '/data/attributes/value',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			// TODO: with options and missing value
			// TODO: with value for non-option-button
			'when including end_date' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionType.id%',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The end date field cannot be present.',
							'source' => [
								'pointer' => '/data/attributes/end_date',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			"when using another user's action_type" => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeOtherUser.id%',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The action type does not belong to the current user.',
							'source' => [
								'pointer' => '/data/relationships/action_type',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when action_type does not exist' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '12345678901234567890',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The action type does not belong to the current user.',
							'source' => [
								'pointer' => '/data/relationships/action_type',
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
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionType.id%',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '?include=action_type',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => null,
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionType.id%',
									'type' => 'action-types',
								],
							],
						],
					],
					'included' => [
						[
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
				],
				'code' => 201,
			]],
			// TODO: with value for options-button
			'with minimal valid attributes for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '123',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeNumber.id%',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '?include=action_type',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '123',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeNumber.id%',
									'type' => 'action-types',
								],
							],
						],
					],
					'included' => [
						[
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
		$tokens = [
			'%actionType.id%' => $this->actionType->id,
			'%actionTypeNumber.id%' => $this->actionTypeNumber->id,
			'%actionTypeOtherUser.id%' => $this->actionTypeOtherUser->id,
		];
		$args['body'] = $this->replaceTokens($tokens, $args['body']);
		$args['response'] = $this->replaceTokens($tokens, $args['response']);
		$response = $this->actingAs($this->user)->json('POST', $this->path . $args['params'], $args['body']);
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
				'key' => 'actionOtherUser',
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
				'key' => 'action',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => null,
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
		$args['response'] = $this->replaceToken('%id%', $this->action->id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->{$args['key']}->id);
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public function updateProvider()
	{
		return [
			"with another user's record" => [[
				'key' => 'actionOtherUser',
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
			'with invalid start_date format' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => 'foo',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The start date does not match the format Y-m-d H:i:s.',
							'source' => [
								'pointer' => '/data/attributes/start_date',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			// TODO: with too long value
			// TODO: when removing value for number
			// TODO: when removing value for option-button
			// TODO: when adding value for non-option-button
			'with invalid end_date format' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'end_date' => 'foo',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The end date does not match the format Y-m-d H:i:s.',
							'source' => [
								'pointer' => '/data/attributes/end_date',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when setting end_date before start_date' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'end_date' => '2000-01-01 01:01:01',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The end date must be after the start date.',
							'source' => [
								'pointer' => '/data/attributes/end_date',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'when setting start_date after new end_date' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2002-02-02 02:02:02',
							'end_date' => '2001-01-01 01:01:01',
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The end date must be after the start date.',
							'source' => [
								'pointer' => '/data/attributes/end_date',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			// TODO: when setting start_date after existing end_date
			'when changing action_type' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '1',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'response' => [
					'errors' => [
						[
							'title' => 'The action type cannot be changed.',
							'source' => [
								'pointer' => '/data/relationships/action_type',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with valid start_date' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2002-02-02 02:02:02',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2002-02-02 02:02:02',
							'end_date' => null,
							'value' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with valid end_date' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'end_date' => '2002-02-02 02:02:02',
						],
					],
				],
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => '2002-02-02 02:02:02',
							'value' => null,
						],
					],
				],
				'code' => 200,
			]],
			// TODO: with valid value for number
			// TODO: with valid value for option-button
		];
	}

	/**
	 * @dataProvider updateProvider
	 */
	public function testUpdate($args)
	{
		$args['body'] = $this->replaceToken('%id%', $this->action->id, $args['body']);
		$args['response'] = $this->replaceToken('%id%', $this->action->id, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->{$args['key']}->id, $args['body']);
		$response->assertExactJson($args['response'])
			->assertStatus($args['code']);
	}

	public function destroyProvider()
	{
		return [
			"with another user's record" => [[
				'key' => 'actionOtherUser',
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
				'key' => 'action',
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
