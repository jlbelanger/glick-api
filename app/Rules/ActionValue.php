<?php

namespace App\Rules;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\Validation\ImplicitRule;

class ActionValue implements ImplicitRule
{
	protected $actionType;
	protected $value;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action $action
	 * @param  array  $data
	 * @return void
	 */
	public function __construct(Action $action, array $data)
	{
		$this->actionType = $action->actionType;
		$this->value = $action->value;

		$id = !empty($data['relationships']['action_type']['data']['id']) ? $data['relationships']['action_type']['data']['id'] : null;
		if ($id) {
			$this->actionType = ActionType::find($id);
		}
		if (!empty($data['attributes']) && array_key_exists('value', $data['attributes'])) {
			$this->value = $data['attributes']['value'];
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
		if ($this->actionType->field_type === 'number') {
			return $this->value !== null && $this->value !== '';
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
		if ($this->actionType->field_type === 'number') {
			return 'The :attribute is required.';
		}
		return 'The :attribute cannot be present.';
	}
}
