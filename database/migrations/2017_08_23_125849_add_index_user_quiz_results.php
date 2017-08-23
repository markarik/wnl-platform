<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexUserQuizResults extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_quiz_results', function (Blueprint $table) {
			// this is the default MySQL index name
			$table->index('user_id', 'user_quiz_results_user_id_index');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_quiz_results', function (Blueprint $table) {
			$table->dropIndex('user_quiz_results_user_id_index');
		});
	}
}
