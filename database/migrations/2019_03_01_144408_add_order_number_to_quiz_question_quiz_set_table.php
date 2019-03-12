<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderNumberToQuizQuestionQuizSetTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('quiz_question_quiz_set', function (Blueprint $table) {
			$table->integer('order_number')->default(1000)->after('quiz_set_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('quiz_question_quiz_set', function (Blueprint $table) {
			$table->dropColumn('order_number');
		});
	}
}
