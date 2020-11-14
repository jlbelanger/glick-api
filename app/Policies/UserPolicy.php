<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine if the given user can be viewed by the user.
	 *
	 * @param  \App\Models\User $currentUser
	 * @param  \App\Models\User $user
	 * @return bool
	 */
	public function view(User $currentUser, User $user)
	{
		return $currentUser->id === $user->id;
	}

	/**
	 * Determine if the given user can be created by the user.
	 *
	 * @param  \App\Models\User $currentUser
	 * @param  \App\Models\User $user
	 * @return bool
	 */
	public function create(User $currentUser, User $user)
	{
		return false;
	}

	/**
	 * Determine if the given user can be deleted by the user.
	 *
	 * @param  \App\Models\User $currentUser
	 * @param  \App\Models\User $user
	 * @return bool
	 */
	public function delete(User $currentUser, User $user)
	{
		return $this->view($currentUser, $user);
	}

	/**
	 * Determine if the given user can be updated by the user.
	 *
	 * @param  \App\Models\User $currentUser
	 * @param  \App\Models\User $user
	 * @return bool
	 */
	public function update(User $currentUser, User $user)
	{
		return $this->view($currentUser, $user);
	}

	/**
	 * Determine if the given user can be viewed by the user.
	 *
	 * @param  \App\Models\User $currentUser
	 * @param  \App\Models\User $user
	 * @return bool
	 */
	public function viewAny(User $currentUser, User $user)
	{
		return false;
	}
}
