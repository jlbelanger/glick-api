<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Jlbelanger\Tapioca\Exceptions\ValidationException;
use Jlbelanger\Tapioca\Helpers\Utilities;
use Validator;

class AuthController extends Controller
{
	/**
	 * Handles an authentication attempt.
	 *
	 * @param  Request $request
	 * @return Response
	 */
	public function login(Request $request) : JsonResponse
	{
		$data = $request->input('data');
		$rules = [
			'attributes.username' => 'required',
			'attributes.password' => 'required',
		];
		$validator = Validator::make($data, $rules, [], Utilities::prettyAttributeNames($rules));
		if ($validator->fails()) {
			$errors = ValidationException::formatErrors($validator->errors()->toArray());
			return response()->json(['errors' => $errors], 422);
		}

		$credentials = [
			'username' => $data['attributes']['username'],
			'password' => $data['attributes']['password'],
		];
		$remember = !empty($data['attributes']['remember']);
		if (!Auth::attempt($credentials, $remember)) {
			return response()->json(['errors' => [['title' => 'Username or password is incorrect.', 'status' => '401']]], 401);
		}

		$user = User::where('username', '=', $credentials['username'])->first();
		$token = $user->createToken('api');

		return response()->json([
			'token' => $token->plainTextToken,
			'user' => $user->getAuthInfo($remember),
		]);
	}

	/**
	 * Logs the user out (Invalidate the token).
	 *
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function logout(Request $request) : JsonResponse // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
	{
		Auth::guard('sanctum')->user()->currentAccessToken()->delete();

		return response()->json(null, 204);
	}

	/**
	 * Handles an authentication attempt.
	 *
	 * @param  Request $request
	 * @return Response
	 */
	public function register(Request $request) : JsonResponse
	{
		$data = $request->input('data');
		$rules = [
			'attributes.username' => 'required|max:255|unique:users,username',
			'attributes.email' => 'required|email|max:255|unique:users,email',
			'attributes.password' => 'required|confirmed',
			'attributes.password_confirmation' => 'required',
		];
		$validator = Validator::make($data, $rules, [], Utilities::prettyAttributeNames($rules));
		if ($validator->fails()) {
			$errors = ValidationException::formatErrors($validator->errors()->toArray());
			return response()->json(['errors' => $errors], 422);
		}

		DB::beginTransaction();
		$user = User::create($data['attributes']);
		$user->forceFill([
			'password' => Hash::make($data['attributes']['password']),
		])->save();
		$user->save();
		$token = $user->createToken('api');
		DB::commit();

		return response()->json([
			'token' => $token->plainTextToken,
			'user' => $user->getAuthInfo(false),
		]);
	}

	/**
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function forgotPassword(Request $request) : JsonResponse
	{
		$data = $request->input('data');
		$rules = [
			'attributes.email' => 'required|email',
		];
		$validator = Validator::make($data, $rules, [], Utilities::prettyAttributeNames($rules));
		if ($validator->fails()) {
			$errors = ValidationException::formatErrors($validator->errors()->toArray());
			return response()->json(['errors' => $errors], 422);
		}

		try {
			Password::sendResetLink(['email' => $data['attributes']['email']]);
		} catch (Exception $e) {
			return response()->json(['errors' => [['title' => 'We were unable to send a password reset email. Please try again later.', 'status' => '500']]], 500);
		}

		return response()->json(null, 204);
	}

	/**
	 * @param  Request $request
	 * @param  string  $token
	 * @return JsonResponse
	 */
	public function resetPassword(Request $request, string $token) : JsonResponse
	{
		$data = $request->input('data');
		$rules = [
			'attributes.email' => 'required|email',
			'attributes.new_password' => 'required|confirmed',
			'attributes.new_password_confirmation' => 'required',
		];
		$validator = Validator::make($data, $rules, [], Utilities::prettyAttributeNames($rules));
		if ($validator->fails()) {
			$errors = ValidationException::formatErrors($validator->errors()->toArray());
			return response()->json(['errors' => $errors], 422);
		}
		if (!empty($errors)) {
			return response()->json(['errors' => $errors], 422);
		}

		$status = Password::reset(
			[
				'email' => $data['attributes']['email'],
				'password' => $data['attributes']['new_password'],
				'password_confirmation' => $data['attributes']['new_password_confirmation'],
				'token' => $token,
			],
			function ($user, $password) use ($request) {
				$user->forceFill([
					'password' => Hash::make($password),
				])->save();

				$user->setRememberToken(Str::random(60));

				event(new PasswordReset($user));
			}
		);

		if ($status !== Password::PASSWORD_RESET) {
			return response()->json(['errors' => [['title' => __($status), 'status' => '422']]], 422);
		}

		return response()->json(null, 204);
	}
}
