<?php

namespace App\Models;

use App\Rules\Action as ActionRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
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

	// ========================================================================
	// JSON API
	// ========================================================================

	/**
	 * @return array
	 */
	public function defaultFilter() : array
	{
		return [
			'action_type.user_id' => [
				'eq' => Auth::guard('sanctum')->id(),
			],
		];
	}

	/**
	 * @return array
	 */
	public function defaultSort() : array
	{
		return ['-start_date'];
	}

	/**
	 * @return array
	 */
	protected function rules(Request $request) : array
	{
		return [
			'attributes.start_date' => 'required',
			'attributes.end_date' => 'after:start_date',
			'attributes.value' => [new ActionRule($this, $request)],
			'relationships.action_type' => 'required',
		];
	}

	/**
	 * @return array
	 */
	public function singularRelationships() : array
	{
		return ['action_type'];
	}

	// ========================================================================
	// Relationships
	// ========================================================================

	/**
	 * @return BelongsTo
	 */
	public function actionType() : BelongsTo
	{
		return $this->belongsTo('App\Models\ActionType');
	}
}
