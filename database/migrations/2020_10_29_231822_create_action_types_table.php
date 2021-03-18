<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionTypesTable extends Migration
{
	/**
	 * Runs the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_types', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained();
			$table->string('label');
			$table->boolean('is_continuous')->default(false);
			$table->enum('field_type', ['button', 'number', 'text']);
			$table->string('suffix')->nullable();
			$table->integer('order_num')->default(0);
			$table->timestamps();
			$table->softDeletes('deleted_at');
		});
	}

	/**
	 * Reverses the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('action_types');
	}
}
