<?php

namespace App\Models;

use App\Models\ActionType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Jlbelanger\Tapioca\Traits\Resource;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
	use HasApiTokens, HasFactory, Notifiable, Resource, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'username',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
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
			'id' => $this->getKey(),
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
	public function rules() : array
	{
		$rules = [
			'data.attributes.username' => [$this->requiredOnCreate(), 'alpha_num', 'max:255', $this->unique('username')],
			'data.attributes.email' => ['prohibited'],
			'data.attributes.password' => ['prohibited'],
		];

		if (Auth::guard('sanctum')->user()->username === 'demo') {
			$rules['data.attributes.username'][] = 'prohibited';
		}

		return $rules;
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
