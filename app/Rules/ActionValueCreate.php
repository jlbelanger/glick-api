<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ImplicitRule;

class ActionValueCreate implements ImplicitRule
{
	protected $actionType;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  ActionType|null $actionType
	 * @return void
	 */
	public function __construct($actionType)
	{
		$this->actionType = $actionType;
	}

	/**
	 * Determines if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return boolean
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
	public function passes($attribute, $value)
	{
		if (!$this->actionType) {
			return true;
		}
		if ($this->actionType->field_type === 'button') {
			return $value === null || $value === '';
		}
		return $value !== null && $value !== '';
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		if ($this->actionType->field_type === 'button') {
			return 'The :attribute cannot be present.';
		}
		return 'The :attribute is required.';
	}
}
