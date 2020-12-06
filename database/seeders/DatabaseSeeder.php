<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seeds the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$date = date('Y-m-d H:i:s');

		DB::table('users')->insert([
			'username' => 'demo',
			'email' => 'demo@example.com',
			'password' => bcrypt('demo'),
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
			'options' => 'Mild, Moderate, Severe',
			'user_id' => $userId,
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
			'options' => 'Mild, Moderate, Severe',
			'user_id' => $userId,
			'created_at' => $date,
		]);
	}
}
