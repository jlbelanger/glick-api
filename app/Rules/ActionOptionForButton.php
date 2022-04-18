<?php

namespace App\Rules;

use App\Models\Action;
use App\Models\ActionType;
use App\Models\Option;
use Illuminate\Contracts\Validation\ImplicitRule;

class ActionOptionForButton implements ImplicitRule
{
	protected $actionType;
	protected $isSettingOption;
	protected $option;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action $action
	 * @param  array  $data
	 * @return void
	 */
	public function __construct(Action $action, array $data)
	{
		$this->actionType = $action->actionType;
		if (!empty($data['relationships']['action_type']['data']['id'])) {
			$this->actionType = ActionType::find($data['relationships']['action_type']['data']['id']);
		}

		$this->isSettingOption = false;
		if (!empty($data['relationships']['option']['data']['id'])) {
			$this->isSettingOption = true;
			$this->option = Option::find($data['relationships']['option']['data']['id']);
		}
	}

	/**
	 * Determines if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return boolean
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceAfterLastUsed
	public function passes($attribute, $value)
	{
		if (!$this->actionType || !$this->isSettingOption) {
			return true;
		}
		return !empty($this->option) && $this->option->action_type_id === $this->actionType->id;
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute does not belong to the action type.';
	}
}
