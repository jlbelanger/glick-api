<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionTypesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_types', function (Blueprint $table) {
			$table->id();
			$table->unsignedInteger('user_id');
			$table->string('label');
			$table->tinyInteger('is_discrete');
			$table->enum('field_type', ['int', 'float', 'string']);
			$table->string('suffix');
			$table->string('options');
			$table->integer('order_num');
			$table->timestamps();
			$table->timestamp('deleted_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('action_types');
	}
}
