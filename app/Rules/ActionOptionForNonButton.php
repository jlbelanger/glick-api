<?php

namespace App\Rules;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\Validation\ImplicitRule;

class ActionOptionForNonButton implements ImplicitRule
{
	protected $actionType;
	protected $hasOption;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action          $action
	 * @param  ActionType|null $actionType
	 * @param  integer|null    $optionId
	 * @return void
	 */
	public function __construct(Action $action, $actionType, $optionId)
	{
		$this->actionType = $actionType;
		$this->hasOption = !empty($action->option_id) || !empty($optionId);
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
		if (!$this->actionType) {
			return true;
		}
		if ($this->actionType->field_type !== 'button') {
			return !$this->hasOption;
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
		return 'The :attribute cannot be present.';
	}
}
