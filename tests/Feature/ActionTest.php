<?php

namespace Tests\Feature;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\Option;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionTest extends TestCase
{
	use RefreshDatabase;

	protected $path = '/actions';

	protected $user;

	protected $otherUser;

	protected $actionType;

	protected $actionTypeOptions;

	protected $actionTypeNumber;

	protected $actionTypeText;

	protected $actionTypeOtherUser;

	protected $optionA;

	protected $optionB;

	protected $optionOtherUser;

	protected $action;

	protected $actionOptions;

	protected $actionNumber;

	protected $actionText;

	protected $actionOtherUser;

	protected $actionWithEndDate;

	protected function setUp() : void
	{
		parent::setUp();
		$this->user = User::factory()->create([]);
		$this->otherUser = User::factory()->create(['email' => 'bar@example.com', 'username' => 'bar']);
		$this->actionType = ActionType::factory()->create(['user_id' => $this->user->id]);
		$this->actionTypeOptions = ActionType::factory()->create(['user_id' => $this->user->id]);
		$this->actionTypeNumber = ActionType::factory()->create(['user_id' => $this->user->id, 'field_type' => 'number']);
		$this->actionTypeText = ActionType::factory()->create(['user_id' => $this->user->id, 'field_type' => 'text']);
		$this->actionTypeOtherUser = ActionType::factory()->create(['user_id' => $this->otherUser->id]);
		$this->optionA = Option::factory()->create(['action_type_id' => $this->actionTypeOptions->id, 'label' => 'A']);
		$this->optionB = Option::factory()->create(['action_type_id' => $this->actionTypeOptions->id, 'label' => 'B']);
		$this->optionOtherUser = Option::factory()->create(['action_type_id' => $this->actionTypeOtherUser->id]);
		$this->action = Action::factory()->create(['action_type_id' => $this->actionType->id]);
		$this->actionOptions = Action::factory()->create(['action_type_id' => $this->actionTypeOptions->id, 'option_id' => $this->optionA->id]);
		$this->actionNumber = Action::factory()->create(['action_type_id' => $this->actionTypeNumber->id, 'value' => '100']);
		$this->actionText = Action::factory()->create(['action_type_id' => $this->actionTypeText->id, 'value' => 'foo']);
		$this->actionOtherUser = Action::factory()->create(['action_type_id' => $this->actionTypeOtherUser->id, 'option_id' => $this->optionOtherUser->id]);
		$this->actionWithEndDate = Action::factory()->create(['action_type_id' => $this->actionType->id, 'start_date' => '2001-01-01 01:00:00', 'end_date' => '2001-01-01 02:00:00']);
	}

	public function testIndex() : void
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
						'notes' => null,
					],
				],
				[
					'id' => (string) $this->actionOptions->id,
					'type' => 'actions',
					'attributes' => [
						'start_date' => '2001-02-03 04:05:06',
						'end_date' => null,
						'value' => null,
						'notes' => null,
					],
				],
				[
					'id' => (string) $this->actionNumber->id,
					'type' => 'actions',
					'attributes' => [
						'start_date' => '2001-02-03 04:05:06',
						'end_date' => null,
						'value' => '100',
						'notes' => null,
					],
				],
				[
					'id' => (string) $this->actionText->id,
					'type' => 'actions',
					'attributes' => [
						'start_date' => '2001-02-03 04:05:06',
						'end_date' => null,
						'value' => 'foo',
						'notes' => null,
					],
				],
				[
					'id' => (string) $this->actionWithEndDate->id,
					'type' => 'actions',
					'attributes' => [
						'start_date' => '2001-01-01 01:00:00',
						'end_date' => '2001-01-01 02:00:00',
						'value' => null,
						'notes' => null,
					],
				],
			],
		]);
		$response->assertStatus(200);
	}

	public static function storeProvider() : array
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
			'with missing value for text' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeText.id%',
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
			'with empty string value for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '',
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
			'with empty string value for text' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeText.id%',
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
			'with null value for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => null,
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
			'with null value for text' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => null,
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeText.id%',
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
			'with non-numeric value for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => 'Foo',
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
							'title' => 'The value must be a number.',
							'source' => [
								'pointer' => '/data/attributes/value',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with value for button' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '0',
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
							'title' => 'The value cannot be present.',
							'source' => [
								'pointer' => '/data/attributes/value',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with option for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '0',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeNumber.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => [
									'id' => '123',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The option cannot be present.',
							'source' => [
								'pointer' => '/data/relationships/option',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with option for text' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '0',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeText.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => [
									'id' => '123',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The option cannot be present.',
							'source' => [
								'pointer' => '/data/relationships/option',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with option that does not exist' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeOptions.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => [
									'id' => '12345678901234567890',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The option does not belong to the action type.',
							'source' => [
								'pointer' => '/data/relationships/option',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with option that does not belong to the action type' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeOptions.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => [
									'id' => '%optionOtherUser.id%',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The option does not belong to the action type.',
							'source' => [
								'pointer' => '/data/relationships/option',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
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
				'params' => '?include=action_type,option',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => null,
							'notes' => null,
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionType.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => null,
							],
						],
					],
					'included' => [
						[
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
				],
				'code' => 201,
			]],
			'with valid option for button' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeOptions.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => [
									'id' => '%optionA.id%',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '?include=action_type,option',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => null,
							'notes' => null,
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeOptions.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => [
									'id' => '%optionA.id%',
									'type' => 'options',
								],
							],
						],
					],
					'included' => [
						[
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
						],
						[
							'id' => '%optionA.id%',
							'type' => 'options',
							'attributes' => [
								'label' => 'A',
								'has_events' => true,
							],
						],
					],
				],
				'code' => 201,
			]],
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
				'params' => '?include=action_type,option',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '123',
							'notes' => null,
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeNumber.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => null,
							],
						],
					],
					'included' => [
						[
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
				],
				'code' => 201,
			]],
			'with zero value for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '0',
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
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '0',
							'notes' => null,
						],
					],
				],
				'code' => 201,
			]],
			'with float value for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '1.5',
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
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '1.5',
							'notes' => null,
						],
					],
				],
				'code' => 201,
			]],
			'with fraction value for number' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => '120/80',
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
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '120/80',
							'notes' => null,
						],
					],
				],
				'code' => 201,
			]],
			'with minimal valid attributes for text' => [[
				'body' => [
					'data' => [
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'value' => 'bar',
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeText.id%',
									'type' => 'action-types',
								],
							],
						],
					],
				],
				'params' => '?include=action_type,option',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => 'bar',
							'notes' => null,
						],
						'relationships' => [
							'action_type' => [
								'data' => [
									'id' => '%actionTypeText.id%',
									'type' => 'action-types',
								],
							],
							'option' => [
								'data' => null,
							],
						],
					],
					'included' => [
						[
							'id' => '%actionTypeText.id%',
							'type' => 'action-types',
							'attributes' => [
								'label' => 'Foo',
								'is_continuous' => false,
								'field_type' => 'text',
								'suffix' => null,
								'order_num' => 0,
								'in_progress' => null,
								'slug' => 'foo',
								'is_archived' => false,
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
		$tokens = [
			'%actionType.id%' => (string) $this->actionType->id,
			'%actionTypeOptions.id%' => (string) $this->actionTypeOptions->id,
			'%actionTypeNumber.id%' => (string) $this->actionTypeNumber->id,
			'%actionTypeText.id%' => (string) $this->actionTypeText->id,
			'%actionTypeOtherUser.id%' => (string) $this->actionTypeOtherUser->id,
			'%optionOtherUser.id%' => (string) $this->optionOtherUser->id,
			'%optionA.id%' => (string) $this->optionA->id,
		];
		$args['body'] = $this->replaceTokens($tokens, $args['body']);
		$args['response'] = $this->replaceTokens($tokens, $args['response']);
		$response = $this->actingAs($this->user)->json('POST', $this->path . $args['params'], $args['body']);
		if (!empty($response['data']['id'])) {
			$args['response'] = $this->replaceToken('%id%', $response['data']['id'], $args['response']);
		}
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public static function showProvider() : array
	{
		return [
			"with another user's record" => [[
				'key' => 'actionOtherUser',
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
				'key' => 'action',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => null,
							'notes' => null,
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
		$args['response'] = $this->replaceToken('%id%', (string) $this->action->id, $args['response']);
		$response = $this->actingAs($this->user)->json('GET', $this->path . '/' . $this->{$args['key']}->id);
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public static function updateProvider() : array
	{
		return [
			"with another user's record" => [[
				'key' => 'actionOtherUser',
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
			'with empty string value for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => '',
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
			'with empty string value for text' => [[
				'key' => 'actionText',
				'body' => [
					'data' => [
						'id' => '%actionText.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => '',
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
			'with null value for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => null,
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
			'with null value for text' => [[
				'key' => 'actionText',
				'body' => [
					'data' => [
						'id' => '%actionText.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => null,
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
			'with non-numeric value for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => 'Foo',
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The value must be a number.',
							'source' => [
								'pointer' => '/data/attributes/value',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with value for button' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'value' => 'Foo',
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The value cannot be present.',
							'source' => [
								'pointer' => '/data/attributes/value',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with option for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => '123',
						],
						'relationships' => [
							'option' => [
								'data' => [
									'id' => '123',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The option cannot be present.',
							'source' => [
								'pointer' => '/data/relationships/option',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with option for text' => [[
				'key' => 'actionText',
				'body' => [
					'data' => [
						'id' => '%actionText.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => '123',
						],
						'relationships' => [
							'option' => [
								'data' => [
									'id' => '123',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The option cannot be present.',
							'source' => [
								'pointer' => '/data/relationships/option',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with option that does not exist' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'relationships' => [
							'option' => [
								'data' => [
									'id' => '12345678901234567890',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The option does not belong to the action type.',
							'source' => [
								'pointer' => '/data/relationships/option',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
			'with option that does not belong to the action type' => [[
				'key' => 'actionOptions',
				'body' => [
					'data' => [
						'id' => '%actionOptions.id%',
						'type' => 'actions',
						'relationships' => [
							'option' => [
								'data' => [
									'id' => '%optionOtherUser.id%',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The option does not belong to the action type.',
							'source' => [
								'pointer' => '/data/relationships/option',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
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
				'params' => '',
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
				'params' => '',
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
				'params' => '',
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
			'when setting start_date after existing end_date' => [[
				'key' => 'actionWithEndDate',
				'body' => [
					'data' => [
						'id' => '%actionWithEndDate.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-01-01 03:00:00',
						],
					],
				],
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The end date must be after the start date.',
							'source' => [
								'pointer' => '/data/attributes/start_date',
							],
							'status' => '422',
						],
					],
				],
				'code' => 422,
			]],
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
				'params' => '',
				'response' => [
					'errors' => [
						[
							'title' => 'The action type field is prohibited.',
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
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2002-02-02 02:02:02',
							'end_date' => null,
							'value' => null,
							'notes' => null,
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
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => '2002-02-02 02:02:02',
							'value' => null,
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with valid value for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => '200',
						],
					],
				],
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '200',
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with zero value for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => '0',
						],
					],
				],
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '0',
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with float value for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => '1.5',
						],
					],
				],
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '1.5',
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with fraction value for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => '120/80',
						],
					],
				],
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '120/80',
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with valid value for text' => [[
				'key' => 'actionText',
				'body' => [
					'data' => [
						'id' => '%actionText.id%',
						'type' => 'actions',
						'attributes' => [
							'value' => 'bar',
						],
					],
				],
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionText.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => 'bar',
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with no attributes for number' => [[
				'key' => 'actionNumber',
				'body' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
					],
				],
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionNumber.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => '100',
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with no attributes for text' => [[
				'key' => 'actionText',
				'body' => [
					'data' => [
						'id' => '%actionText.id%',
						'type' => 'actions',
					],
				],
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%actionText.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => 'foo',
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with no attributes for button' => [[
				'key' => 'action',
				'body' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
					],
				],
				'params' => '',
				'response' => [
					'data' => [
						'id' => '%id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => null,
							'notes' => null,
						],
					],
				],
				'code' => 200,
			]],
			'with valid option for button' => [[
				'key' => 'actionOptions',
				'body' => [
					'data' => [
						'id' => '%actionOptions.id%',
						'type' => 'actions',
						'relationships' => [
							'option' => [
								'data' => [
									'id' => '%optionB.id%',
									'type' => 'options',
								],
							],
						],
					],
				],
				'params' => '?include=option',
				'response' => [
					'data' => [
						'id' => '%actionOptions.id%',
						'type' => 'actions',
						'attributes' => [
							'start_date' => '2001-02-03 04:05:06',
							'end_date' => null,
							'value' => null,
							'notes' => null,
						],
						'relationships' => [
							'option' => [
								'data' => [
									'id' => '%optionB.id%',
									'type' => 'options',
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
			'%id%' => (string) $this->action->id,
			'%actionNumber.id%' => (string) $this->actionNumber->id,
			'%actionText.id%' => (string) $this->actionText->id,
			'%actionOptions.id%' => (string) $this->actionOptions->id,
			'%actionTypeOptions.id%' => (string) $this->actionTypeOptions->id,
			'%optionOtherUser.id%' => (string) $this->optionOtherUser->id,
			'%actionWithEndDate.id%' => (string) $this->actionWithEndDate->id,
			'%optionB.id%' => (string) $this->optionB->id,
		];
		$args['body'] = $this->replaceTokens($tokens, $args['body']);
		$args['response'] = $this->replaceTokens($tokens, $args['response']);
		$response = $this->actingAs($this->user)->json('PUT', $this->path . '/' . $this->{$args['key']}->id . $args['params'], $args['body']);
		$response->assertExactJson($args['response']);
		$response->assertStatus($args['code']);
	}

	public static function destroyProvider() : array
	{
		return [
			"with another user's record" => [[
				'key' => 'actionOtherUser',
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
				'key' => 'action',
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
			$response->assertExactJson($args['response']);
			$response->assertStatus($args['code']);
		} else {
			$response->assertNoContent($args['code']);
		}
	}
}
