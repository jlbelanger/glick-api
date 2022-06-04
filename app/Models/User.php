<?php

namespace App\Models;

use App\Models\ActionType;
use App\Rules\CannotChange;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jlbelanger\Tapioca\Traits\Resource;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable, Resource, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'username',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	// ========================================================================
	// JSON API
	// ========================================================================

	/**
	 * @param  boolean $remember
	 * @return array
	 */
	public function getAuthInfo(bool $remember) : array
	{
		return [
			'id' => $this->id,
			'remember' => $remember,
		];
	}

	/**
	 * @return array
	 */
	public function defaultSort() : array
	{
		return ['username'];
	}

	/**
	 * @return array
	 */
	protected function rules() : array
	{
		return [
			'attributes.username' => ['filled', 'alpha_num', 'max:255', 'unique:users,username,' . $this->id],
			'attributes.email' => [new CannotChange()],
			'attributes.password' => [new CannotChange()],
		];
	}

	/**
	 * @return array
	 */
	public function whitelistedAttributes() : array
	{
		return array_merge($this->fillable, ['password_confirmation']);
	}

	// ========================================================================
	// Relationships
	// ========================================================================

	/**
	 * @return HasMany
	 */
	public function actionTypes() : HasMany
	{
		return $this->hasMany(ActionType::class);
	}
}
