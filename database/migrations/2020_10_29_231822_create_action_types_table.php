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
			$table->tinyInteger('is_continuous')->default(0);
			$table->enum('field_type', ['button', 'number']);
			$table->string('suffix')->nullable();
			$table->string('options')->nullable();
			$table->integer('order_num')->default(0);
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
