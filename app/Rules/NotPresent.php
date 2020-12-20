<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotPresent implements Rule
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
		return $value === null;
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute field cannot be present.';
	}
}
