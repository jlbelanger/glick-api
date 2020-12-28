<?php

namespace App\Rules;

use App\Models\Action;
use Illuminate\Contracts\Validation\Rule;

class ActionStartEndDate implements Rule
{
	protected $startDate;
	protected $endDate;
	protected $isSettingStartDate;
	protected $isSettingEndDate;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action $action
	 * @param  array  $data
	 * @return void
	 */
	public function __construct(Action $action, array $data)
	{
		$this->isSettingStartDate = false;
		$this->isSettingEndDate = false;
		$this->startDate = $action->start_date;
		$this->endDate = $action->end_date;

		if (!empty($data['attributes']['start_date'])) {
			$this->isSettingStartDate = true;
			$this->startDate = $data['attributes']['start_date'];
		}
		if (!empty($data['attributes']['end_date'])) {
			$this->isSettingEndDate = true;
			$this->endDate = $data['attributes']['end_date'];
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
		if ($this->isSettingStartDate && $this->isSettingEndDate && $attribute === 'attributes.start_date') {
			return true;
		}
		if (!$this->endDate) {
			return true;
		}
		return $this->startDate <= $this->endDate;
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The end date must be after the start date.';
	}
}
