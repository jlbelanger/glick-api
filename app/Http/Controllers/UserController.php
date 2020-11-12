<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jlbelanger\LaravelJsonApi\Controllers\ResourceController;
use Jlbelanger\LaravelJsonApi\Traits\Validatable;
use Validator;

class UserController extends ResourceController
{
	/**
	 * @param  \Illuminate\Http\Request $request
	 * @param  string                   $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function changePassword(Request $request, string $id)
	{
		$data = $request->input('data');
		$rules = [
			'password' => 'required',
			'new_password' => 'required|confirmed',
			'new_password_confirmation' => 'required',
		];
		$validator = Validator::make($data['attributes'], $rules);
		if ($validator->fails()) {
			$errors = Validatable::formatErrors($validator->errors()->toArray());
			return response()->json(['errors' => $errors], 422);
		}
		if (!empty($errors)) {
			return response()->json(['errors' => $errors], 422);
		}

		$user = User::find($id);
		if (!Hash::check($data['attributes']['password'], $user->password)) {
			$error = [
				'title' => 'Current password is incorrect.',
				'source' => [
					'pointer' => '/data/attributes/password',
				],
				'status' => '422',
			];
			return response()->json(['errors' => [$error]], 422);
		}

		$user->forceFill([
			'password' => Hash::make($data['attributes']['new_password']),
		])->save();
		$user->setRememberToken(Str::random(60));
		event(new PasswordReset($user));

		return response()->json(null, 204);
	}
}
