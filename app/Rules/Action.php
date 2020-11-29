<?php

namespace App\Rules;

use App\Models\Action as ActionModel;
use App\Models\ActionType;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Http\Request;

class Action implements ImplicitRule
{
	protected $actionType;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action $action
	 * @return void
	 */
	public function __construct(ActionModel $action, Request $request)
	{
		$this->actionType = $action->actionType;
		if (!$this->actionType) {
			$data = $request->get('data');
			$this->actionType = ActionType::find($data['relationships']['action_type']['data']['id']);
		}
	}

	/**
	 * Determines if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
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
