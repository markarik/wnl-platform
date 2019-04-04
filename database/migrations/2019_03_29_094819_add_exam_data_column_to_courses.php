<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExamDataColumnToCourses extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('courses', function (Blueprint $table) {
			$table->integer('entry_exam_tag_id')->nullable();
			$table->integer('entry_exam_lesson_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('courses', function (Blueprint $table) {
			$table->dropColumn('entry_exam_tag_id');
			$table->dropColumn('entry_exam_lesson_id');
		});
	}
}
