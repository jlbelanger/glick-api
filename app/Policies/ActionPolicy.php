<?php

namespace App\Policies;

use App\Models\Action;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActionPolicy
{
	use HandlesAuthorization;

	/**
	 * Determines if the given action can be viewed by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Action $action
	 * @return boolean
	 */
	public function view(User $currentUser, Action $action) : bool
	{
		return $currentUser->id === $action->actionType->user_id;
	}

	/**
	 * Determines if the given action can be created by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Action $action
	 * @return boolean
	 */
	public function create(User $currentUser, Action $action) : bool
	{
		return true;
	}

	/**
	 * Determines if the given action can be deleted by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Action $action
	 * @return boolean
	 */
	public function delete(User $currentUser, Action $action) : bool
	{
		return $this->view($currentUser, $action);
	}

	/**
	 * Determines if the given action can be updated by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Action $action
	 * @return boolean
	 */
	public function update(User $currentUser, Action $action) : bool
	{
		return $this->view($currentUser, $action);
	}

	/**
	 * Determines if the given action can be viewed by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Action $action
	 * @return boolean
	 */
	public function viewAny(User $currentUser, Action $action) : bool
	{
		return true;
	}
}
