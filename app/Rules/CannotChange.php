<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CannotChange implements Rule
{
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
		return $value === null;
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute cannot be changed.';
	}
}
