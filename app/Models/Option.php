<?php

namespace App\Models;

use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Jlbelanger\Tapioca\Traits\Resource;

class Option extends Model
{
	use HasFactory, Resource, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'action_type_id',
		'label',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array
	 */
	protected $casts = [
		'action_type_id' => 'integer',
	];

	// ========================================================================
	// Attributes
	// ========================================================================

	/**
	 * @return boolean
	 */
	public function getHasEventsAttribute() : bool
	{
		return $this->actions()->exists();
	}

	// ========================================================================
	// JSON API
	// ========================================================================

	/**
	 * @return array
	 */
	public function additionalAttributes() : array
	{
		return ['has_events'];
	}

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
	public function rules() : array
	{
		return [
			'data.attributes.label' => [$this->requiredOnCreate(), 'max:255'],
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
	 * @return HasMany
	 */
	public function actions() : HasMany
	{
		return $this->hasMany(Action::class);
	}

	/**
	 * @return BelongsTo
	 */
	public function actionType() : BelongsTo
	{
		return $this->belongsTo(ActionType::class);
	}
}
