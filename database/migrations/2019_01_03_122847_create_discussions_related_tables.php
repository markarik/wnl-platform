<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscussionsRelatedTables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('screens', function (Blueprint $table) {
			$table->discussable();
		});

		Schema::table('pages', function (Blueprint $table) {
			$table->discussable();
		});

		Schema::table('qna_questions', function (Blueprint $table) {
			$table->integer('discussion_id')->nullable();
		});

		Schema::create('discussions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
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
		Schema::table('screens', function (Blueprint $table) {
			$table->dropDiscussable();
		});

		Schema::table('pages', function (Blueprint $table) {
			$table->dropDiscussable();
		});

		Schema::table('qna_questions', function (Blueprint $table) {
			$table->dropColumn('discussion_id');
		});

		Schema::dropIfExists('discussions');
	}
}
