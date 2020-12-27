<?php

namespace App\Policies;

use App\Models\Option;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OptionPolicy
{
	use HandlesAuthorization;

	/**
	 * Determines if the given action can be viewed by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Option $option
	 * @return boolean
	 */
	public function view(User $currentUser, Option $option) : bool
	{
		return $currentUser->id === $action->actionType->user_id;
	}

	/**
	 * Determines if the given action can be created by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Option $option
	 * @return boolean
	 */
	public function create(User $currentUser, Option $option) : bool
	{
		return true;
	}

	/**
	 * Determines if the given action can be deleted by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Option $option
	 * @return boolean
	 */
	public function delete(User $currentUser, Option $option) : bool
	{
		return $this->view($currentUser, $action);
	}

	/**
	 * Determines if the given action can be updated by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Option $option
	 * @return boolean
	 */
	public function update(User $currentUser, Option $option) : bool
	{
		return $this->view($currentUser, $action);
	}

	/**
	 * Determines if the given action can be viewed by the user.
	 *
	 * @param  User   $currentUser
	 * @param  Option $option
	 * @return boolean
	 */
	public function viewAny(User $currentUser, Option $option) : bool
	{
		return true;
	}
}
