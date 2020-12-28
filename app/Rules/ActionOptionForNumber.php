<?php

namespace App\Rules;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\Validation\ImplicitRule;

class ActionOptionForNumber implements ImplicitRule
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
		$this->hasOption = !empty($action->option_id);

		$id = !empty($data['relationships']['action_type']['data']['id']) ? $data['relationships']['action_type']['data']['id'] : null;
		if ($id) {
			$this->actionType = ActionType::find($id);
		}
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
		if ($this->actionType->field_type === 'number') {
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
