<?php

namespace App\Rules;

use App\Models\ActionType;
use Illuminate\Contracts\Validation\Rule;

class ActionTypeOptions implements Rule
{
	protected $fieldType;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  ActionType $actionType
	 * @param  array      $data
	 * @return void
	 */
	public function __construct(ActionType $actionType, array $data)
	{
		$this->fieldType = $actionType->field_type;
		if (!empty($data['attributes']['field_type'])) {
			$this->fieldType = $data['attributes']['field_type'];
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
	public function passes($attribute, $value)
	{
		if ($this->fieldType === 'button') {
			return true;
		}
		return empty($value);
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
