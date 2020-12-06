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
		$this->isSettingActionType = false;
		$this->actionType = $action->actionType;

		$data = $request->get('data');
		$id = !empty($data['relationships']['action_type']['data']['id']) ? $data['relationships']['action_type']['data']['id'] : null;
		if ($id) {
			$this->isSettingActionType = true;
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
	public function passes($attribute, $value) // phpcs:disable Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
	{
		if (!$this->isSettingActionType || !$this->actionType) {
			return true;
		}
		if ($this->actionType->field_type === 'number' || strpos($this->actionType->options, ',') !== false) {
			return !empty($value);
		}
		return true;
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute is required.';
	}
}
