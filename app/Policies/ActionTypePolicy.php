<?php

namespace App\Policies;

use App\Models\ActionType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActionTypePolicy
{
	use HandlesAuthorization;

	/**
	 * Determines if the given actionType type can be viewed by the user.
	 *
	 * @param  User       $currentUser
	 * @param  ActionType $actionType
	 * @return boolean
	 */
	public function view(User $currentUser, ActionType $actionType) : bool
	{
		return $currentUser->id === $actionType->user_id;
	}

	/**
	 * Determines if the given action type can be created by the user.
	 *
	 * @param  User       $currentUser
	 * @param  ActionType $actionType
	 * @return boolean
	 */
	public function create(User $currentUser, ActionType $actionType) : bool
	{
		return true;
	}

	/**
	 * Determines if the given action type can be deleted by the user.
	 *
	 * @param  User       $currentUser
	 * @param  ActionType $actionType
	 * @return boolean
	 */
	public function delete(User $currentUser, ActionType $actionType) : bool
	{
		return $this->view($currentUser, $actionType);
	}

	/**
	 * Determines if the given action type can be updated by the user.
	 *
	 * @param  User       $currentUser
	 * @param  ActionType $actionType
	 * @return boolean
	 */
	public function update(User $currentUser, ActionType $actionType) : bool
	{
		return $this->view($currentUser, $actionType);
	}

	/**
	 * Determines if the given action type can be viewed by the user.
	 *
	 * @param  User       $currentUser
	 * @param  ActionType $actionType
	 * @return boolean
	 */
	public function viewAny(User $currentUser, ActionType $actionType) : bool
	{
		return true;
	}
}
