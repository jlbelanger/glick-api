<?php

namespace App\Rules;

use App\Models\ActionType;
use Illuminate\Contracts\Validation\ImplicitRule;

class ActionValueCreate implements ImplicitRule
{
	protected $actionType;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  array $data
	 * @return void
	 */
	public function __construct(array $data)
	{
		if (!empty($data['relationships']['action_type']['data']['id'])) {
			$this->actionType = ActionType::find($data['relationships']['action_type']['data']['id']);
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
