<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Runs the migrations.
	 *
	 * @return void
	 */
	public function up() : void
	{
		Schema::table('action_types', function (Blueprint $table) {
			$table->boolean('is_archived')->default(false);
		});
	}

	/**
	 * Reverses the migrations.
	 *
	 * @return void
	 */
	public function down() : void
	{
		Schema::table('action_types', function (Blueprint $table) {
			$table->dropColumn('is_archived');
		});
	}
};
