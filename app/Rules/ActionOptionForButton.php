<?php

namespace App\Rules;

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
	 * @param  ActionType|null $actionType
	 * @param  integer|null    $optionId
	 * @return void
	 */
	public function __construct($actionType, $optionId)
	{
		$this->actionType = $actionType;
		$this->isSettingOption = !empty($optionId);
		if (!empty($optionId)) {
			$this->option = Option::find($optionId);
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
