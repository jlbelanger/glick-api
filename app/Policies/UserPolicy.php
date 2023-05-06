<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
	use HandlesAuthorization;

	/**
	 * Determines if the given user can be viewed by the user.
	 *
	 * @param  User $currentUser
	 * @param  User $user
	 * @return boolean
	 */
	public function view(User $currentUser, User $user) : bool
	{
		return $currentUser->id === $user->id;
	}

	/**
	 * Determines if the given user can be created by the user.
	 *
	 * @param  User $currentUser
	 * @param  User $user
	 * @return boolean
	 */
	public function create(User $currentUser, User $user) : bool // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	{
		return false;
	}

	/**
	 * Determines if the given user can be deleted by the user.
	 *
	 * @param  User $currentUser
	 * @param  User $user
	 * @return boolean
	 */
	public function delete(User $currentUser, User $user) : bool
	{
		return $this->view($currentUser, $user) && $currentUser->username !== 'demo';
	}

	/**
	 * Determines if the given user can be updated by the user.
	 *
	 * @param  User $currentUser
	 * @param  User $user
	 * @return boolean
	 */
	public function update(User $currentUser, User $user) : bool
	{
		return $this->view($currentUser, $user);
	}

	/**
	 * Determines if the given user can be viewed by the user.
	 *
	 * @param  User $currentUser
	 * @param  User $user
	 * @return boolean
	 */
	public function viewAny(User $currentUser, User $user) : bool // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	{
		return false;
	}
}
