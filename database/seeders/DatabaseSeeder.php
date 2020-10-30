<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
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
			'is_discrete' => true,
			'field_type' => 'float',
			'suffix' => 'lbs',
			'user_id' => 1,
		]);

		DB::table('action_types')->insert([
			'label' => 'Temperature',
			'is_discrete' => true,
			'field_type' => 'float',
			'suffix' => '&deg;C',
			'user_id' => 1,
		]);

		DB::table('action_types')->insert([
			'label' => 'Sleep',
			'is_discrete' => false,
			'user_id' => 1,
		]);

		DB::table('action_types')->insert([
			'label' => 'Headache',
			'is_discrete' => false,
			'field_type' => 'string',
			'options' => 'Mild,Moderate,Severe',
			'user_id' => 1,
		]);
	}
}
