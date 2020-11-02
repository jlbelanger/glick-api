<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
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
		'options',
		'order_num',
	];

	protected $additional = [
		'slug',
	];

	protected $oneRelationships = [
		'user',
	];

	protected $manyRelationships = [];

	// ========================================================================
	// Attributes
	// ========================================================================

	public function getSlugAttribute()
	{
		return Str::slug($this->label);
	}

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
	// Mutators
	// ========================================================================

	public function setOptionsAttribute($value)
	{
		$value = explode(',', $value);
		$value = array_map('trim', $value);
		$value = implode(', ', $value);
		$this->attributes['options'] = $value;
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
