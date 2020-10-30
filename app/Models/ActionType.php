<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jlbelanger\LaravelJsonApi\Traits\Resource;

class ActionType extends Model
{
	use HasFactory, Resource;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'label',
		'is_discrete',
		'field_type',
		'suffix',
		'options',
		'order_num',
	];

	protected $additional = [];

	protected $oneRelationships = [
		'user',
	];

	protected $manyRelationships = [];

	// ========================================================================
	// JSON API
	// ========================================================================

	public function defaultSort()
	{
		return ['order_num', 'label'];
	}

	protected function requiredRelationships()
	{
		return ['user'];
	}

	protected function rules()
	{
		return [
			'label' => 'required',
			'field_type' => 'required',
		];
	}

	// ========================================================================
	// Relationships
	// ========================================================================

	public function actions()
	{
		return $this->hasMany('App\Models\Action');
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
