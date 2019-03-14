<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionAndLessonIdToQuizSetsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('quiz_sets', function (Blueprint $table) {
			$table->integer('lesson_id')->after('name');
			$table->text('description')->nullable()->after('lesson_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('quiz_sets', function (Blueprint $table) {
			$table->dropColumn('lesson_id');
			$table->dropColumn('description');
		});
	}
}
