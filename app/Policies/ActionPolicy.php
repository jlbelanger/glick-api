<?php

namespace App\Policies;

use App\Models\Action;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActionPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine if the given action can be viewed by the user.
	 *
	 * @param  \App\Models\User   $currentUser
	 * @param  \App\Models\Action $action
	 * @return bool
	 */
	public function view(User $currentUser, Action $action)
	{
		return $currentUser->id === $action->actionType->user_id;
	}

	/**
	 * Determine if the given action can be created by the user.
	 *
	 * @param  \App\Models\User   $currentUser
	 * @param  \App\Models\Action $action
	 * @return bool
	 */
	public function create(User $currentUser, Action $action)
	{
		return $this->view($currentUser, $action);
	}

	/**
	 * Determine if the given action can be destroyed by the user.
	 *
	 * @param  \App\Models\User   $currentUser
	 * @param  \App\Models\Action $action
	 * @return bool
	 */
	public function destroy(User $currentUser, Action $action)
	{
		return $this->view($currentUser, $action);
	}

	/**
	 * Determine if the given action can be updated by the user.
	 *
	 * @param  \App\Models\User   $currentUser
	 * @param  \App\Models\Action $action
	 * @return bool
	 */
	public function update(User $currentUser, Action $action)
	{
		return $this->view($currentUser, $action);
	}

	/**
	 * Determine if the given action can be viewed by the user.
	 *
	 * @param  \App\Models\User   $currentUser
	 * @param  \App\Models\Action $action
	 * @return bool
	 */
	public function viewAny(User $currentUser, Action $action)
	{
		return $this->view($currentUser, $action);
	}
}
