<?php

namespace App\Rules;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\Validation\ImplicitRule;

class ActionOptionForNonButton implements ImplicitRule
{
	protected $actionType;
	protected $hasOption;

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
		if (!empty($data['relationships']['action_type']['data']['id'])) {
			$this->actionType = ActionType::find($data['relationships']['action_type']['data']['id']);
		}

		$this->hasOption = !empty($action->option_id);
		if (!empty($data['relationships']['option']['data'])) {
			$this->hasOption = true;
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
		if ($this->actionType->field_type !== 'button') {
			return !$this->hasOption;
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
		return 'The :attribute cannot be present.';
	}
}
