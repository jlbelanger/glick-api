<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Jlbelanger\LaravelJsonApi\Traits\Resource;

class Action extends Model
{
	use HasFactory, Resource, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'action_type_id',
		'start_date',
		'end_date',
		'value',
	];

	protected $additional = [];

	protected $oneRelationships = [
		'action_type',
	];

	protected $manyRelationships = [];

	// ========================================================================
	// JSON API
	// ========================================================================

	public function defaultFilter()
	{
		return [
			'action_type.user_id' => [
				'eq' => Auth::guard('sanctum')->user()->id,
			],
		];
	}

	public function defaultSort()
	{
		return ['-start_date'];
	}

	protected function requiredRelationships()
	{
		return ['action_type'];
	}

	protected function rules()
	{
		return [
			'start_date' => 'required',
		];
	}

	// ========================================================================
	// Relationships
	// ========================================================================

	public function actionType()
	{
		return $this->belongsTo('App\Models\ActionType');
	}
}
