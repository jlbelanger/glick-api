<?php

namespace App\Models;

use App\Models\ActionType;
use App\Models\Option;
use App\Rules\ActionActionType;
use App\Rules\ActionOptionForButton;
use App\Rules\ActionOptionForNonButton;
use App\Rules\ActionStartEndDate;
use App\Rules\ActionValueCreate;
use App\Rules\ActionValueNumeric;
use App\Rules\ActionValueUpdate;
use App\Rules\NotPresent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Jlbelanger\Tapioca\Traits\Resource;

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
		'option_id',
		'start_date',
		'end_date',
		'value',
		'notes',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'action_type_id' => 'integer',
		'option_id' => 'integer',
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
	public function rules() : array
	{
		$actionType = $this->actionType;
		if (!empty(request()->input('data.relationships.action_type.data.id'))) {
			$actionType = ActionType::find(request()->input('data.relationships.action_type.data.id'));
		}
		$optionId = request()->input('data.relationships.option.data.id');

		$rules = [
			'data.attributes.notes' => ['nullable', 'max:65535'],
			'data.relationships.option' => [
				'bail',
				new ActionOptionForNonButton($this, $actionType, $optionId),
				new ActionOptionForButton($actionType, $optionId),
			],
		];

		if ($this->getKey()) {
			$originalStartDate = $this->start_date;
			$originalEndDate = $this->end_date;
			$startDate = request()->input('data.attributes.start_date');
			$endDate = request()->input('data.attributes.end_date');
			$rules['data.attributes.value'] = ['bail', new ActionValueUpdate($this), new ActionValueNumeric($this->actionType)];
			$rules['data.attributes.start_date'] = ['bail', 'date_format:"Y-m-d H:i:s"', new ActionStartEndDate($originalStartDate, $originalEndDate, $startDate, $endDate)];
			$rules['data.attributes.end_date'] = ['bail', 'nullable', 'date_format:"Y-m-d H:i:s"', new ActionStartEndDate($originalStartDate, $originalEndDate, $startDate, $endDate)];
			$rules['data.relationships.action_type'] = ['prohibited'];
		} else {
			$rules['data.attributes.value'] = ['bail', new ActionValueCreate($actionType), new ActionValueNumeric($actionType)];
			$rules['data.attributes.start_date'] = ['bail', 'required', 'date_format:"Y-m-d H:i:s"'];
			$rules['data.attributes.end_date'] = [new NotPresent()];
			$rules['data.relationships.action_type'] = ['required', new ActionActionType($actionType)];
		}

		return $rules;
	}

	/**
	 * @return array
	 */
	public function singularRelationships() : array
	{
		return ['action_type', 'option'];
	}

	// ========================================================================
	// Relationships
	// ========================================================================

	/**
	 * @return BelongsTo
	 */
	public function actionType() : BelongsTo
	{
		return $this->belongsTo(ActionType::class);
	}

	/**
	 * @return BelongsTo
	 */
	public function option() : BelongsTo
	{
		return $this->belongsTo(Option::class);
	}
}
