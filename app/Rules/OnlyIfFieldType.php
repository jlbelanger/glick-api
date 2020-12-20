<?php

namespace App\Rules;

use App\Models\ActionType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class OnlyIfFieldType implements Rule
{
	protected $requestFieldType;
	protected $allowedFieldType;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Request    $request
	 * @param  string     $allowedFieldType
	 * @param  ActionType $actionType
	 * @return void
	 */
	public function __construct(Request $request, string $allowedFieldType, ActionType $actionType)
	{
		$data = $request->get('data');
		$this->requestFieldType = !empty($data['attributes']['field_type']) ? $data['attributes']['field_type'] : null;
		if ($request->method() === 'PUT' && !$this->requestFieldType) {
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
