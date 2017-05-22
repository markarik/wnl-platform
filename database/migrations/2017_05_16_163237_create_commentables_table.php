<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentablesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commentables', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('comment_id');
			$table->unsignedInteger('commentable_id');
			$table->string('commentable_type');
			$table->timestamps();

			$table
				->foreign('comment_id')
				->references('id')
				->on('comments')
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
		Schema::dropIfExists('commentables');
	}
}
