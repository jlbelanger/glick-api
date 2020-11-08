<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
		$credentials = $request->only('username', 'password');
		$remember = $request->input('remember');

		if (!Auth::attempt($credentials, $remember)) {
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
		$user = new User();
		$errors = $user->validate($data, 'create');
		if ($errors) {
			return response()->json(['errors' => $errors], 422);
		}

		DB::beginTransaction();
		$user->create($data['attributes']);
		$token = $user->createToken('api');
		DB::commit();

		return response()->json(['id' => $user->id, 'token' => $token->plainTextToken]);
	}
}
