<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('creator_id');
			$table->unsignedInteger('assignee_id');
			$table->smallInteger('priority');
			$table->integer('order');
			$table->string('status');
			$table->text('text');
			$table->json('labels');
			$table->json('context');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tasks');
	}
}
