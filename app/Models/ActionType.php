<?php

namespace App\Models;

use App\Models\Action;
use App\Models\Option;
use App\Models\User;
use App\Rules\ActionTypeOptions;
use App\Rules\CannotChange;
use App\Rules\CannotRemoveWithEvents;
use App\Rules\NotPresent;
use App\Rules\OnlyIfFieldType;
use App\Rules\TempIdsOnly;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jlbelanger\LaravelJsonApi\Traits\Resource;

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
	 * @return array
	 */
	public function defaultAttributes() : array
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
	 * @param  array  $data
	 * @param  string $method
	 * @return array
	 */
	protected function rules(array $data, string $method) : array
	{
		$rules = [
			'attributes.label' => ['max:255'],
			'attributes.suffix' => ['bail', new OnlyIfFieldType($data, $method, 'number', $this), 'max:255'],
			'attributes.order_num' => ['integer'],
			'relationships.options' => [new ActionTypeOptions($this, $data)],
		];
		if ($method === 'POST') {
			$rules['attributes.label'][] = 'required';
			$rules['attributes.field_type'] = ['bail', 'required', Rule::in(['button', 'number', 'text'])];
			$rules['attributes.is_continuous'] = ['bail', new OnlyIfFieldType($data, $method, 'button', $this), 'boolean'];
			$rules['relationships.options'][] = new TempIdsOnly();
			$rules['relationships.user'] = [new NotPresent()];
		} elseif ($method === 'PUT') {
			$rules['attributes.label'][] = 'filled';
			$rules['attributes.field_type'] = [new CannotChange()];
			$rules['attributes.is_continuous'] = [new CannotChange()];
			$rules['relationships.options'][] = new CannotRemoveWithEvents($this);
			$rules['relationships.user'] = [new CannotChange()];
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
