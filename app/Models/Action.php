<?php

namespace App\Models;

use App\Models\ActionType;
use App\Rules\ActionActionType;
use App\Rules\ActionValue;
use App\Rules\CannotChange;
use App\Rules\NotPresent;
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
	 * @param  Request $request
	 * @return array
	 */
	protected function rules(Request $request) : array
	{
		$rules = [
			'attributes.start_date' => ['date_format:"Y-m-d H:i:s"'],
			'attributes.value' => [new ActionValue($this, $request), 'max:255'],
		];
		$method = $request->method();
		if ($method === 'POST') {
			$rules['attributes.start_date'][] = ['required'];
			$rules['attributes.end_date'] = [new NotPresent()];
			$rules['relationships.action_type'] = ['required', new ActionActionType($this, $request)];
		} elseif ($method === 'PUT') {
			$rules['attributes.end_date'] = ['bail', 'date_format:"Y-m-d H:i:s"', 'after:start_date'];
			$rules['relationships.action_type'] = [new CannotChange()];
		}
		return $rules;
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
		return $this->belongsTo(ActionType::class);
	}
}
