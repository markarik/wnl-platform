<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class AddDetailsToUserCourseProgress extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_course_progress', function (Blueprint $table) {
			$table->timestamps();
			$table->unsignedInteger('screen_id')->after('lesson_id')->nullable();
			$table->unsignedInteger('section_id')->after('screen_id')->nullable();
			$table->unique(
				[
					'user_id',
					'lesson_id',
					'section_id',
					'screen_id'
				],
				'unique_user_course_detailed_progress'
			);

			try {
				$table->dropUnique('unique_user_course_progress');
			} catch (QueryException $e) {
				// it means index already dropped
			}
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_course_progress', function (Blueprint $table) {
			$table->dropTimestamps();
			$table->dropColumn(['section_id', 'screen_id']);
			$table->dropUnique('unique_user_course_detailed_progress');
		});
	}
}
