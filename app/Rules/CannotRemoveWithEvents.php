<?php

namespace App\Rules;

use App\Models\ActionType;
use App\Models\Option;
use Illuminate\Contracts\Validation\Rule;

class CannotRemoveWithEvents implements Rule
{
	protected $optionIds;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  ActionType $actionType
	 * @return void
	 */
	public function __construct(ActionType $actionType)
	{
		$this->optionIds = $actionType->options()->pluck('id')->toArray();
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
		$newOptionIds = array_column($value['data'], 'id');
		$removedOptionIds = array_diff($this->optionIds, $newOptionIds);
		if (empty($removedOptionIds)) {
			return true;
		}
		$options = Option::whereIn('id', $removedOptionIds)->get();
		foreach ($options as $option) {
			if ($option->hasEvents) {
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
		return 'Options with existing events cannot be removed.';
	}
}
