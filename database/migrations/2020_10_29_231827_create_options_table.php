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
		Schema::create('options', function (Blueprint $table) {
			$table->id();
			$table->foreignId('action_type_id')->constrained();
			$table->string('label');
			$table->timestamps();
			$table->softDeletes('deleted_at');
		});
	}

	/**
	 * Reverses the migrations.
	 *
	 * @return void
	 */
	public function down() : void
	{
		Schema::dropIfExists('options');
	}
};
