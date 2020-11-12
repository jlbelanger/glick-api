<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jlbelanger\LaravelJsonApi\Traits\Resource;
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

	protected $additional = [];

	protected $oneRelationships = [];

	protected $manyRelationships = [];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	// ========================================================================
	// JSON API
	// ========================================================================

	public function actionTypes()
	{
		return $this->hasMany('App\Models\ActionType');
	}

	public function defaultSort()
	{
		return ['username'];
	}

	protected function rules()
	{
		return [
			'username' => 'required|max:255|unique:users,username' . ($this->id ? ',' . $this->id : ''),
			'email' => 'required|email|max:255|unique:users,email' . ($this->id ? ',' . $this->id : ''),
			'password' => 'required|confirmed',
		];
	}

	public function whitelistedAttributes()
	{
		return array_merge($this->fillable, ['password_confirmation']);
	}
}
