<?php

namespace App\Rules;

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
	 * @param  string|null $originalStartDate
	 * @param  string|null $originalEndDate
	 * @param  string|null $startDate
	 * @param  string|null $endDate
	 * @return void
	 */
	public function __construct($originalStartDate, $originalEndDate, $startDate, $endDate)
	{
		$this->startDate = $startDate ? $startDate : $originalStartDate;
		$this->isSettingStartDate = $startDate && $startDate !== $originalStartDate;
		$this->endDate = $endDate ? $endDate : $originalEndDate;
		$this->isSettingEndDate = $endDate && $endDate !== $originalEndDate;
	}

	/**
	 * Determines if the validation rule passes.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return boolean
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceAfterLastUsed
	public function passes($attribute, $value)
	{
		if ($this->isSettingStartDate && $this->isSettingEndDate && $attribute === 'data.attributes.start_date') {
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
