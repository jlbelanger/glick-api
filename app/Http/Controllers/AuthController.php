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
use Log;
use Jlbelanger\Tapioca\Helpers\Utilities;

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
		$rules = [
			'data.attributes.username' => ['required', 'string'],
			'data.attributes.password' => ['required', 'string'],
			'data.attributes.remember' => ['boolean'],
		];
		$this->validate($request, $rules, [], Utilities::prettyAttributeNames($rules));

		$credentials = [
			'username' => $request->input('data.attributes.username'),
			'password' => $request->input('data.attributes.password'),
		];
		$remember = !empty($request->input('data.attributes.remember'));
		if (!Auth::attempt($credentials, $remember)) {
			self::logWarning(['action' => 'login', 'username' => $credentials['username']]);
			return response()->json(['errors' => [['title' => __('auth.failed'), 'status' => '401']]], 401);
		}

		$user = User::where('username', '=', $credentials['username'])->first();
		if ($user instanceof MustVerifyEmail && !$user->email_verified_at) {
			self::logWarning(['action' => 'login', 'email' => $user->email, 'info' => 'unverified']);
			return response()->json(['errors' => [['title' => __('auth.unverified'), 'status' => '401', 'code' => 'auth.unverified']]], 401);
		}

		self::log(['action' => 'login', 'email' => $user->email]);
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
		$user = Auth::guard('sanctum')->user();
		$user->currentAccessToken()->delete();
		self::log(['action' => 'logout', 'email' => $user->email]);

		return response()->json(null, 204);
	}

	/**
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function forgotPassword(Request $request) : JsonResponse
	{
		$rules = [
			'data.attributes.email' => ['required', 'email'],
		];
		$this->validate($request, $rules, [], Utilities::prettyAttributeNames($rules));

		$email = $request->input('data.attributes.email');
		try {
			$status = Password::sendResetLink(['email' => $email]);
		} catch (Exception $e) {
			self::logWarning(['action' => 'forgotPassword', 'email' => $email]);
			return response()->json(['errors' => [['title' => __('passwords.send_error'), 'status' => '500']]], 500);
		}

		$success = $status === Password::RESET_LINK_SENT;
		if ($status === Password::RESET_LINK_SENT) {
			self::log(['action' => 'forgotPassword', 'email' => $email]);
		} else {
			self::logWarning(['action' => 'forgotPassword', 'email' => $email, 'info' => $status]);
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
		$rules = [
			'data.attributes.email' => ['required', 'email'],
			'data.attributes.new_password' => ['required', 'confirmed', Rules\Password::defaults()],
		];
		$this->validate($request, $rules, [], Utilities::prettyAttributeNames($rules));

		$email = $request->input('data.attributes.email');
		if ($request->query('expires') < Carbon::now()->timestamp) {
			self::logWarning(['action' => 'resetPassword', 'email' => $email, 'info' => 'expired']);
			return response()->json(['errors' => [['title' => __('passwords.expired'), 'status' => '403']]], 403);
		}

		$status = Password::reset(
			[
				'email' => $email,
				'password' => $request->input('data.attributes.new_password'),
				'password_confirmation' => $request->input('data.attributes.new_password_confirmation'),
				'token' => $token,
			],
			function ($user, $password) use ($email) {
				$userData = [
					'password' => Hash::make($password),
					'remember_token' => Str::random(60),
				];
				if ($user instanceof MustVerifyEmail && !$user->email_verified_at) {
					$userData['email_verified_at'] = Carbon::now();
				}
				$user->forceFill($userData)->save();
				self::log(['action' => 'resetPassword', 'email' => $user->email]);

				event(new PasswordReset($user));
			}
		);

		if ($status !== Password::PASSWORD_RESET) {
			self::logWarning(['action' => 'resetPassword', 'email' => $email, 'info' => $status]);
			if ($status === 'passwords.user') {
				$status = 'passwords.token';
			}
			return response()->json(['errors' => [['title' => __($status), 'status' => '403']]], 403);
		}

		return response()->json(null, 204);
	}

	/**
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function changeEmail(Request $request) : JsonResponse
	{
		$user = Auth::guard('sanctum')->user();
		if ($user->username === 'demo') {
			abort(403, 'You do not have permission to update this record.');
		}

		$rules = [
			'data.attributes.password' => ['required', 'current_password:sanctum'],
			'data.attributes.email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->getKey()],
		];
		$this->validate($request, $rules, [], Utilities::prettyAttributeNames($rules));

		$user->email = $request->input('data.attributes.email');
		if ($user instanceof MustVerifyEmail && !$user->email_verified_at) {
			$user->email_verified_at = null;
		}
		$user->save();
		self::log(['action' => 'changeEmail', 'email' => $user->email]);

		return response()->json(null, 204);
	}

	/**
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function changePassword(Request $request) : JsonResponse
	{
		$user = Auth::guard('sanctum')->user();
		if ($user->username === 'demo') {
			abort(403, 'You do not have permission to update this record.');
		}

		$rules = [
			'data.attributes.password' => ['required', 'current_password:sanctum'],
			'data.attributes.new_password' => ['required', 'confirmed', Rules\Password::defaults()],
		];
		$this->validate($request, $rules, [], Utilities::prettyAttributeNames($rules));

		$user->forceFill([
			'password' => Hash::make($request->input('data.attributes.new_password')),
			'remember_token' => Str::random(60),
		])->save();
		event(new PasswordReset($user));
		self::log(['action' => 'changePassword', 'email' => $user->email]);

		return response()->json(null, 204);
	}

	/**
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function register(Request $request) : JsonResponse
	{
		$rules = [
			'data.attributes.username' => ['required', 'alpha_num', 'max:255', 'unique:users,username'],
			'data.attributes.email' => ['required', 'email', 'max:255', 'unique:users,email'],
			'data.attributes.password' => ['required', 'confirmed', Rules\Password::defaults()],
			'data.attributes.password_confirmation' => ['required'],
		];
		$this->validate($request, $rules, [], Utilities::prettyAttributeNames($rules));

		DB::beginTransaction();
		$user = User::create($request->input('data.attributes'));
		$user->forceFill([
			'password' => Hash::make($request->input('data.attributes.password')),
		])->save();
		$user->save();
		event(new Registered($user));
		self::log(['action' => 'register', 'email' => $user->email]);
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
	public function verifyEmail(Request $request) : JsonResponse
	{
		$user = User::find($request->query('id'));
		if (!$user->hasVerifiedEmail()) {
			$user->markEmailAsVerified();
			event(new Verified($user));
			self::log(['action' => 'verifyEmail', 'email' => $user->email]);
		}
		return response()->json(null, 204);
	}

	/**
	 * @param  Request $request
	 * @return JsonResponse
	 */
	public function resendVerification(Request $request) : JsonResponse
	{
		$user = User::where('username', '=', $request->input('username'))->first();
		if ($user) {
			$user->sendEmailVerificationNotification();
			self::log(['action' => 'resendVerification', 'email' => $user->email]);
		}
		return response()->json(null, 204);
	}

	protected static function logWarning($s) : void
	{
		self::log($s, 'warning');
	}

	protected static function log($s, string $level = 'info') : void
	{
		Log::channel('auth')->$level(json_encode(array_merge($s, ['ip' => request()->ip()])));
	}
}
