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
			$table->unsignedInteger('notifiable_id')->nullable();
			$table->string('notifiable_type')->nullable();
			$table->string('team')->nullable();
			$table->string('subject_type')->nullable();
			$table->unsignedInteger('subject_id')->nullable();
			$table->unsignedInteger('creator_id')->nullable();
			$table->unsignedInteger('assignee_id')->nullable();
			$table->smallInteger('priority')->nullable();
			$table->integer('order')->nullable();
			$table->string('status')->nullable();
			$table->text('text')->nullable();
			$table->json('labels')->nullable();
			$table->timestamps();
			$table->index(['subject_type', 'subject_id']);
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
