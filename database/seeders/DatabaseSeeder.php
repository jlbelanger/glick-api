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
		DB::table('users')->insert([
			'username' => 'jenny',
			'email' => 'jenny@example.com',
			'password' => bcrypt('test'),
		]);

		DB::table('action_types')->insert([
			'label' => 'Weight',
			'is_continuous' => false,
			'field_type' => 'number',
			'suffix' => 'lbs',
			'user_id' => 1,
		]);

		DB::table('action_types')->insert([
			'label' => 'Temperature',
			'is_continuous' => false,
			'field_type' => 'number',
			'suffix' => '°C',
			'user_id' => 1,
		]);

		DB::table('action_types')->insert([
			'label' => 'Sleep',
			'is_continuous' => true,
			'field_type' => 'button',
			'user_id' => 1,
		]);

		DB::table('action_types')->insert([
			'label' => 'Headache',
			'is_continuous' => true,
			'field_type' => 'button',
			'options' => 'Mild, Moderate, Severe',
			'user_id' => 1,
		]);
	}
}
