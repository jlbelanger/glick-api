<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
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
			'attributes.username' => ['required'],
			'attributes.password' => ['required'],
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
			return response()->json(['errors' => [['title' => __('auth.failed'), 'status' => '401']]], 401);
		}

		$user = User::where('username', '=', $credentials['username'])->first();
		if ($user instanceof MustVerifyEmail && !$user->email_verified_at) {
			return response()->json(['errors' => [['title' => __('auth.unverified'), 'status' => '401', 'code' => 'auth.unverified']]], 401);
		}

		return response()->json([
			'token' => $user->createToken('api')->plainTextToken,
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
			'attributes.username' => ['required', 'alpha_num', 'max:255', 'unique:users,username'],
			'attributes.email' => ['required', 'email', 'max:255', 'unique:users,email'],
			'attributes.password' => ['required', 'confirmed', Rules\Password::defaults()],
			'attributes.password_confirmation' => ['required'],
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
		event(new Registered($user));
		DB::commit();

		if ($user instanceof MustVerifyEmail) {
			return response()->json(null, 204);
		}

		return response()->json([
			'token' => $user->createToken('api')->plainTextToken,
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
			'attributes.email' => ['required', 'email'],
		];
		$validator = Validator::make($data, $rules, [], Utilities::prettyAttributeNames($rules));
		if ($validator->fails()) {
			$errors = ValidationException::formatErrors($validator->errors()->toArray());
			return response()->json(['errors' => $errors], 422);
		}

		try {
			Password::sendResetLink(['email' => $data['attributes']['email']]);
		} catch (Exception $e) {
			return response()->json(['errors' => [['title' => __('passwords.send_error'), 'status' => '500']]], 500);
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
			'attributes.email' => ['required', 'email'],
			'attributes.new_password' => ['required', 'confirmed', Rules\Password::defaults()],
		];
		$validator = Validator::make($data, $rules, [], Utilities::prettyAttributeNames($rules));
		if ($validator->fails()) {
			$errors = ValidationException::formatErrors($validator->errors()->toArray());
			return response()->json(['errors' => $errors], 422);
		}

		if ($request->query('expires') < Carbon::now()->timestamp) {
			return response()->json(['errors' => [['title' => __('passwords.expired'), 'status' => '403']]], 403);
		}

		$status = Password::reset(
			[
				'email' => $data['attributes']['email'],
				'password' => $data['attributes']['new_password'],
				'password_confirmation' => $data['attributes']['new_password_confirmation'],
				'token' => $token,
			],
			function ($user, $password) {
				$userData = [
					'password' => Hash::make($password),
					'remember_token' => Str::random(60),
				];
				if ($user instanceof MustVerifyEmail && !$user->email_verified_at) {
					$userData['email_verified_at'] = Carbon::now();
				}
				$user->forceFill($userData)->save();

				event(new PasswordReset($user));
			}
		);

		if ($status !== Password::PASSWORD_RESET) {
			if ($status === 'passwords.user') {
				$status = 'passwords.token';
			}
			return response()->json(['errors' => [['title' => __($status), 'status' => '422']]], 422);
		}

		return response()->json(null, 204);
	}

	/**
	 * @param  Request $request
	 * @return Response
	 */
	public function verifyEmail(Request $request) : JsonResponse
	{
		$user = User::find($request->query('id'));
		if (!$user->hasVerifiedEmail()) {
			$user->markEmailAsVerified();
			event(new Verified($user));
		}
		return response()->json(null, 204);
	}

	/**
	 * @param  Request $request
	 * @return Response
	 */
	public function resendVerification(Request $request) : JsonResponse
	{
		$user = User::where('username', '=', $request->input('username'))->first();
		if ($user) {
			$user->sendEmailVerificationNotification();
		}
		return response()->json(null, 204);
	}
}
