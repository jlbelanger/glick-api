<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
	return response()->json(['success' => true]);
});

Route::group(['middleware' => ['api', 'guest']], function () {
	Route::post('/auth/login', '\App\Http\Controllers\AuthController@login');
	Route::post('/auth/register', '\App\Http\Controllers\AuthController@register');
	Route::post('/auth/forgot-password', '\App\Http\Controllers\AuthController@forgotPassword');
	Route::put('/auth/reset-password/{token}', '\App\Http\Controllers\AuthController@resetPassword');
});

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::delete('/auth/logout', '\App\Http\Controllers\AuthController@logout');
	Route::put('/users/{id}/change-email', '\App\Http\Controllers\UserController@changeEmail');
	Route::put('/users/{id}/change-password', '\App\Http\Controllers\UserController@changePassword');

	Route::apiResources([
		'actions' => '\App\Http\Controllers\ActionController',
		'action-types' => '\App\Http\Controllers\ActionTypeController',
		'users' => '\App\Http\Controllers\UserController',
	]);
});

Route::fallback(function () {
	return response()->json(['errors' => [['title' => 'URL does not exist.', 'status' => '404']]], 404);
});
