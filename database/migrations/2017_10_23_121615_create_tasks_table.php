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
			$table->uuid('id');
			$table->primary('id');
			$table->morphs('notifiable');
			$table->string('team');
			$table->uuid('event_id')->nullable()->index();
			$table->unsignedInteger('creator_id')->nullable();
			$table->unsignedInteger('assignee_id')->nullable();
			$table->smallInteger('priority')->nullable();
			$table->integer('order')->nullable();
			$table->string('status')->nullable();
			$table->text('text')->nullable();
			$table->json('labels')->nullable();
			$table->json('context')->nullable();
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
