<?php

namespace App\Rules;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Http\Request;

class ActionValue implements ImplicitRule
{
	protected $actionType;
	protected $isSettingActionType;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action  $action
	 * @param  Request $request
	 * @return void
	 */
	public function __construct(Action $action, Request $request)
	{
		$this->actionType = $action->actionType;

		$data = $request->get('data');
		$id = !empty($data['relationships']['action_type']['data']['id']) ? $data['relationships']['action_type']['data']['id'] : null;
		if ($id) {
			$this->actionType = ActionType::find($id);
		}
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
			return true;
		}
		if ($this->actionType->field_type === 'number') {
			return !empty($value);
		}
		return empty($value);
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		if ($this->actionType->field_type === 'number') {
			return 'The :attribute is required.';
		}
		return 'The :attribute cannot be set.';
	}
}
