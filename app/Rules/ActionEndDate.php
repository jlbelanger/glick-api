<?php

namespace App\Rules;

use App\Models\Action;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

class ActionEndDate implements Rule
{
	protected $actionType;
	protected $userId;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action  $action
	 * @param  Request $request
	 * @return void
	 */
	public function __construct(Action $action, Request $request)
	{
		$this->startDate = $action->start_date;
		$data = $request->get('data');
		if (!empty($data['attributes']['start_date'])) {
			$this->startDate = $data['attributes']['start_date'];
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
		if (!$this->startDate) {
			return false;
		}
		return $value >= $this->startDate;
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute must be after the start date.';
	}
}
