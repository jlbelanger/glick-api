<?php

use Illuminate\Support\Facades\Route;
use Jlbelanger\Tapioca\Exceptions\NotFoundException;

Route::get('/', function () {
	return response()->json(['success' => true]);
});

Route::group(['middleware' => ['api', 'guest']], function () {
	Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login']);
	Route::post('/auth/register', [\App\Http\Controllers\AuthController::class, 'register']);
	Route::post('/auth/forgot-password', [\App\Http\Controllers\AuthController::class, 'forgotPassword']);
	Route::put('/auth/reset-password/{token}', [\App\Http\Controllers\AuthController::class, 'resetPassword']);
});

Route::group(['middleware' => ['api', 'auth:sanctum']], function () {
	Route::delete('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
	Route::put('/users/{id}/change-email', [\App\Http\Controllers\UserController::class, 'changeEmail']);
	Route::put('/users/{id}/change-password', [\App\Http\Controllers\UserController::class, 'changePassword']);
	Route::post('/users/delete-data', [\App\Http\Controllers\UserController::class, 'deleteData']);

	Route::apiResources([
		'actions' => \App\Http\Controllers\ActionController::class,
		'action-types' => \App\Http\Controllers\ActionTypeController::class,
		'users' => \App\Http\Controllers\UserController::class,
	]);
});

Route::fallback(function () {
	throw NotFoundException::generate();
});
