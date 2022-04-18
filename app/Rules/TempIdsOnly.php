<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TempIdsOnly implements Rule
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
		foreach ($value['data'] as $option) {
			if (strpos($option['id'], 'temp-') !== 0) {
				return false;
			}
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
		return 'The :attribute cannot contain existing options.';
	}
}
