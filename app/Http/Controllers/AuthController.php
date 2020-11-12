<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Jlbelanger\LaravelJsonApi\Traits\Validatable;
use Validator;

class AuthController extends Controller
{
	/**
	 * Handle an authentication attempt.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return Response
	 */
	public function login(Request $request)
	{
		$data = $request->input('data');
		$rules = [
			'username' => 'required',
			'password' => 'required',
		];
		$validator = Validator::make($data['attributes'], $rules);
		if ($validator->fails()) {
			$errors = Validatable::formatErrors($validator->errors()->toArray());
			return response()->json(['errors' => $errors], 422);
		}

		$credentials = [
			'username' => $data['attributes']['username'],
			'password' => $data['attributes']['password'],
		];
		if (!Auth::attempt($credentials, !empty($data['attributes']['remember']))) {
			return response()->json(['errors' => [['title' => 'Username or password is incorrect.', 'status' => '401']]], 401);
		}

		$user = User::where('username', '=', $credentials['username'])->first();
		$token = $user->createToken('api');

		return response()->json(['id' => $user->id, 'token' => $token->plainTextToken]);
	}

	/**
	 * Log the user out (Invalidate the token).
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function logout(Request $request)
	{
		Auth::logout();
		// $request->user()->currentAccessToken()->delete(); // TODO

		return response()->json(null, 204);
	}

	/**
	 * Handle an authentication attempt.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return Response
	 */
	public function register(Request $request)
	{
		$data = $request->input('data');
		$rules = [
			'username' => 'required|max:255|unique:users,username',
			'email' => 'required|email|max:255|unique:users,email',
			'password' => 'required|confirmed',
			'password_confirmation' => 'required',
		];
		$validator = Validator::make($data['attributes'], $rules);
		if ($validator->fails()) {
			$errors = Validatable::formatErrors($validator->errors()->toArray());
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

		return response()->json(['id' => $user->id, 'token' => $token->plainTextToken]);
	}

	/**
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function forgotPassword(Request $request)
	{
		$data = $request->input('data');
		$rules = [
			'email' => 'required|email',
		];
		$validator = Validator::make($data['attributes'], $rules);
		if ($validator->fails()) {
			$errors = Validatable::formatErrors($validator->errors()->toArray());
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
	 * @param  \Illuminate\Http\Request $request
	 * @param  string                   $token
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function resetPassword(Request $request, string $token)
	{
		$data = $request->input('data');
		$rules = [
			'email' => 'required|email',
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
