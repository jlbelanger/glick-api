<?php

namespace App\Models;

use App\Models\Action;
use App\Models\Option;
use App\Models\User;
use App\Rules\CannotRemoveWithEvents;
use App\Rules\NotPresent;
use App\Rules\TempIdsOnly;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jlbelanger\Tapioca\Traits\Resource;

class ActionType extends Model
{
	use HasFactory, Resource, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'label',
		'is_continuous',
		'field_type',
		'suffix',
		'order_num',
		'is_archived',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'user_id' => 'integer',
		'is_continuous' => 'boolean',
		'order_num' => 'integer',
		'is_archived' => 'boolean',
	];

	// ========================================================================
	// Attributes
	// ========================================================================

	/**
	 * @return array|null
	 */
	public function getInProgressAttribute()
	{
		if (!$this->is_continuous) {
			return null;
		}
		$actions = $this->actions()->whereNull('end_date')->select(['id', 'start_date', 'option_id']);
		if (!$actions->exists()) {
			return null;
		}
		$action = $actions->first();
		return [
			'id' => (string) $action->id,
			'start_date' => $action->start_date,
			'option' => [
				'id' => (string) $action->option_id,
				'type' => 'options',
			],
		];
	}

	/**
	 * @return string
	 */
	public function getSlugAttribute() : string
	{
		return Str::slug($this->label);
	}

	// ========================================================================
	// JSON API
	// ========================================================================

	/**
	 * @return array
	 */
	public function additionalAttributes() : array
	{
		return ['in_progress', 'slug'];
	}

	/**
	 * @param  array $data
	 * @return array
	 */
	public function defaultAttributes(array $data) : array // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
	{
		return [
			'user_id' => Auth::guard('sanctum')->id(),
		];
	}

	/**
	 * @return array
	 */
	public function defaultFilter() : array
	{
		return [
			'user_id' => [
				'eq' => Auth::guard('sanctum')->id(),
			],
		];
	}

	/**
	 * @return array
	 */
	public function defaultSort() : array
	{
		return ['order_num', 'label'];
	}

	/**
	 * @return array
	 */
	public function rules() : array
	{
		$fieldType = request()->input('data.attributes.field_type', $this->field_type);
		$rules = [
			'data.attributes.label' => [$this->requiredOnCreate(), 'max:255'],
			'data.attributes.suffix' => ['max:255'],
			'data.attributes.order_num' => ['integer'],
			'data.attributes.is_archived' => ['boolean'],
			'data.relationships.options' => [],
		];
		if ($fieldType !== 'number') {
			$rules['data.attributes.suffix'][] = 'prohibited';
		}
		if ($fieldType !== 'button') {
			$rules['data.relationships.options'][] = 'prohibited';
		}
		if ($this->getKey()) {
			$rules['data.attributes.field_type'] = ['prohibited'];
			$rules['data.attributes.is_continuous'] = ['prohibited'];
			$rules['data.relationships.options'][] = new CannotRemoveWithEvents($this);
			$rules['data.relationships.user'] = ['prohibited'];
		} else {
			$rules['data.attributes.field_type'] = ['bail', 'required', Rule::in(['button', 'number', 'text'])];
			$rules['data.attributes.is_continuous'] = ['boolean'];
			$rules['data.relationships.options'][] = new TempIdsOnly();
			$rules['data.relationships.user'] = [new NotPresent()];
			if ($fieldType !== 'button') {
				$rules['data.attributes.is_continuous'][] = 'prohibited';
			}
		}
		return $rules;
	}

	/**
	 * @return array
	 */
	public function singularRelationships() : array
	{
		return ['user'];
	}

	/**
	 * @return array
	 */
	public function multiRelationships() : array
	{
		return ['actions', 'options'];
	}

	// ========================================================================
	// Relationships
	// ========================================================================

	/**
	 * @return HasMany
	 */
	public function actions() : HasMany
	{
		return $this->hasMany(Action::class)->orderBy('start_date', 'DESC');
	}

	/**
	 * @return HasMany
	 */
	public function options() : HasMany
	{
		return $this->hasMany(Option::class);
	}

	/**
	 * @return BelongsTo
	 */
	public function user() : BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
