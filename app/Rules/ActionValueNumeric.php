<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ActionValueNumeric implements Rule
{
	/**
	 * Determines if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return boolean
	 */
	public function passes($attribute, $value) // phpcs:ignore Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
	{
		if (preg_match('/^\d+(\.\d+)?$/', $value)) {
			return true;
		}
		return preg_match('/^\d+\/\d+$/', $value);
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute must be a number.';
	}
}
