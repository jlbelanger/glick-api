<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Cross-Origin Resource Sharing (CORS) Configuration
	|--------------------------------------------------------------------------
	|
	| Here you may configure your settings for cross-origin resource sharing
	| or "CORS". This determines what cross-origin operations may execute
	| in web browsers. You are free to adjust these settings as needed.
	|
	| To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
	|
	*/

	'paths' => ['*'],

	'allowed_methods' => ['OPTIONS', 'GET', 'PUT', 'POST', 'DELETE'],

	'allowed_origins' => [env('FRONTEND_URL'), env('APP_URL')],

	'allowed_origins_patterns' => [],

	'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],

	'exposed_headers' => [],

	'max_age' => 0,

	'supports_credentials' => false,

];
