<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seeds the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$date = Carbon::now();

		DB::table('users')->insert([
			'username' => 'demo',
			'email' => 'demo@example.com',
			'email_verified_at' => $date,
			'password' => Hash::make('demo'),
			'created_at' => $date,
		]);
		$userId = DB::getPdo()->lastInsertId();

		DB::table('action_types')->insert([
			'label' => 'Weight',
			'is_continuous' => false,
			'field_type' => 'number',
			'suffix' => 'lbs',
			'user_id' => $userId,
			'created_at' => $date,
		]);

		DB::table('action_types')->insert([
			'label' => 'Temperature',
			'is_continuous' => false,
			'field_type' => 'number',
			'suffix' => '°C',
			'user_id' => $userId,
			'created_at' => $date,
		]);

		DB::table('action_types')->insert([
			'label' => 'Sleep',
			'is_continuous' => true,
			'field_type' => 'button',
			'user_id' => $userId,
			'created_at' => $date,
		]);

		DB::table('action_types')->insert([
			'label' => 'Headache',
			'is_continuous' => true,
			'field_type' => 'button',
			'user_id' => $userId,
			'created_at' => $date,
		]);
		$headacheId = DB::getPdo()->lastInsertId();

		DB::table('options')->insert([
			'label' => 'Mild',
			'action_type_id' => $headacheId,
			'created_at' => $date,
		]);

		DB::table('options')->insert([
			'label' => 'Moderate',
			'action_type_id' => $headacheId,
			'created_at' => $date,
		]);

		DB::table('options')->insert([
			'label' => 'Severe',
			'action_type_id' => $headacheId,
			'created_at' => $date,
		]);

		DB::table('action_types')->insert([
			'label' => 'Meal',
			'is_continuous' => false,
			'field_type' => 'button',
			'user_id' => $userId,
			'created_at' => $date,
		]);

		DB::table('action_types')->insert([
			'label' => 'Pain',
			'is_continuous' => false,
			'field_type' => 'button',
			'user_id' => $userId,
			'created_at' => $date,
		]);
		$painId = DB::getPdo()->lastInsertId();

		DB::table('options')->insert([
			'label' => 'Mild',
			'action_type_id' => $painId,
			'created_at' => $date,
		]);

		DB::table('options')->insert([
			'label' => 'Moderate',
			'action_type_id' => $painId,
			'created_at' => $date,
		]);

		DB::table('options')->insert([
			'label' => 'Severe',
			'action_type_id' => $painId,
			'created_at' => $date,
		]);

		DB::table('action_types')->insert([
			'label' => 'Note',
			'is_continuous' => false,
			'field_type' => 'text',
			'user_id' => $userId,
			'created_at' => $date,
		]);
	}
}
