<?php

namespace App\Rules;

use App\Models\ActionType;
use Illuminate\Contracts\Validation\Rule;

class OnlyIfFieldType implements Rule
{
	protected $requestFieldType;
	protected $allowedFieldType;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  array      $data
	 * @param  string     $method
	 * @param  string     $allowedFieldType
	 * @param  ActionType $actionType
	 * @return void
	 */
	public function __construct(array $data, string $method, string $allowedFieldType, ActionType $actionType)
	{
		$this->requestFieldType = !empty($data['attributes']['field_type']) ? $data['attributes']['field_type'] : null;
		if ($method === 'PUT' && !$this->requestFieldType) {
			$this->requestFieldType = $actionType->field_type;
		}
		$this->allowedFieldType = $allowedFieldType;
	}

	/**
	 * Determines if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return boolean
	 */
	public function passes($attribute, $value) // phpcs:ignore Squiz.Commenting.FunctionComment.ScalarTypeHintMissing
	{
		if ($this->requestFieldType === $this->allowedFieldType) {
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
		return 'The :attribute cannot be set unless the field type is "' . $this->allowedFieldType . '".';
	}
}
