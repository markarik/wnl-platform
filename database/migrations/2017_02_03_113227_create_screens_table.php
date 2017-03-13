<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScreensTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('screens', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('lesson_id');
			$table->string('name');
			$table->text('content')->nullable();
			$table->string('type');
			$table->timestamps();

			$table
				->foreign('lesson_id')
				->references('id')
				->on('lessons')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('screens');
	}
}
