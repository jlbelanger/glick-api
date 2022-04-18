<?php

namespace App\Rules;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ActionActionType implements Rule
{
	protected $actionType;
	protected $userId;

	/**
	 * Creates a new rule instance.
	 *
	 * @param  Action $action
	 * @param  array  $data
	 * @return void
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed
	public function __construct(Action $action, array $data)
	{
		if (!empty($data['relationships']['action_type']['data']['id'])) {
			$this->actionType = ActionType::find($data['relationships']['action_type']['data']['id']);
		}
		$this->userId = Auth::guard('sanctum')->id();
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
		if (!$this->actionType) {
			return false;
		}
		return $this->actionType->user_id === $this->userId;
	}

	/**
	 * Gets the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The :attribute does not belong to the current user.';
	}
}
