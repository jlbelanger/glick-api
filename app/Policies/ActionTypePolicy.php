<?php

namespace App\Policies;

use App\Models\ActionType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActionTypePolicy
{
	use HandlesAuthorization;

	/**
	 * Determine if the given actionType type can be viewed by the user.
	 *
	 * @param  \App\Models\User       $currentUser
	 * @param  \App\Models\ActionType $actionType
	 * @return bool
	 */
	public function view(User $currentUser, ActionType $actionType)
	{
		return $currentUser->id === $actionType->user_id;
	}

	/**
	 * Determine if the given action type can be created by the user.
	 *
	 * @param  \App\Models\User       $currentUser
	 * @param  \App\Models\ActionType $actionType
	 * @return bool
	 */
	public function create(User $currentUser, ActionType $actionType)
	{
		return true;
	}

	/**
	 * Determine if the given action type can be deleted by the user.
	 *
	 * @param  \App\Models\User       $currentUser
	 * @param  \App\Models\ActionType $actionType
	 * @return bool
	 */
	public function delete(User $currentUser, ActionType $actionType)
	{
		return $this->view($currentUser, $actionType);
	}

	/**
	 * Determine if the given action type can be updated by the user.
	 *
	 * @param  \App\Models\User       $currentUser
	 * @param  \App\Models\ActionType $actionType
	 * @return bool
	 */
	public function update(User $currentUser, ActionType $actionType)
	{
		return $this->view($currentUser, $actionType);
	}

	/**
	 * Determine if the given action type can be viewed by the user.
	 *
	 * @param  \App\Models\User       $currentUser
	 * @param  \App\Models\ActionType $actionType
	 * @return bool
	 */
	public function viewAny(User $currentUser, ActionType $actionType)
	{
		return true;
	}
}
