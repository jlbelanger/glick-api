<?php

namespace App\Rules;

use App\Models\Action;
use Illuminate\Contracts\Validation\Rule;

class ActionValueUpdate implements Rule
{
	protected $actionType;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action $action
	 * @return void
	 */
	public function __construct(Action $action)
	{
		$this->actionType = $action->actionType;
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
		if ($this->actionType->field_type !== 'button') {
			return $value !== null && $value !== '';
		}
		return $value === null || $value === '';
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		if ($this->actionType->field_type !== 'button') {
			return 'The :attribute is required.';
		}
		return 'The :attribute cannot be present.';
	}
}
