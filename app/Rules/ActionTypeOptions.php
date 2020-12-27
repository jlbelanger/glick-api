<?php

namespace App\Rules;

use App\Models\ActionType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class ActionTypeOptions implements Rule
{
	protected $fieldType;
	protected $hasOptions = false;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  ActionType $actionType
	 * @param  Request    $request
	 * @return void
	 */
	public function __construct(ActionType $actionType, Request $request)
	{
		$this->fieldType = $actionType->fieldType;
		$data = $request->get('data');
		if (!empty($data['attributes']['field_type'])) {
			$this->fieldType = $data['attributes']['field_type'];
		}
		if (!empty($data['relationships']['options'])) {
			$this->hasOptions = true;
		}
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
		if ($this->fieldType === 'button') {
			return true;
		}
		return !$this->hasOptions;
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
