<?php

namespace App\Rules;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionActionType implements Rule
{
	protected $actionType;
	protected $userId;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action  $action
	 * @param  Request $request
	 * @return void
	 */
	public function __construct(Action $action, Request $request)
	{
		$data = $request->get('data');
		$id = !empty($data['relationships']['action_type']['data']['id']) ? $data['relationships']['action_type']['data']['id'] : null;
		if ($id) {
			$this->actionType = ActionType::find($id);
		}
		$this->userId = Auth::guard('sanctum')->id();
	}

	/**
	 * Determines if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return boolean
	 */
	public function passes($attribute, $value) // phpcs:ignore Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
	{
		if (!$this->actionType) {
			return false;
		}
		return $this->actionType->user_id === $this->userId;
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute does not belong to the current user.';
	}
}
