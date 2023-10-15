<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Option;
use App\Models\ActionType;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jlbelanger\Tapioca\Controllers\AuthorizedResourceController;

class UserController extends AuthorizedResourceController
{
	/**
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function deleteData(Request $request) : JsonResponse
	{
		$types = $request->input('types');
		if (empty($types)) {
			return response()->json(['message' => 'Please select at least one type of data to delete.'], 422);
		}

		DB::beginTransaction();
		$user = Auth::guard('sanctum')->user();

		if (in_array('events', $types) || in_array('event types', $types)) {
			Action::whereHas('actionType', function ($q) use ($user) {
				$q->where('user_id', '=', $user->getKey());
			})->delete();
		}

		if (in_array('event types', $types)) {
			Option::whereHas('actionType', function ($q) use ($user) {
				$q->where('user_id', '=', $user->getKey());
			})->delete();
			ActionType::where('user_id', '=', $user->getKey())->delete();
		}

		DB::commit();

		return response()->json(null, 204);
	}
}
