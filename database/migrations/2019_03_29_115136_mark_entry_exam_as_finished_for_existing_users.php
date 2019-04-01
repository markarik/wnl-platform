<?php

use Illuminate\Database\Migrations\Migration;

class MarkEntryExamAsFinishedForExistingUsers extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// FIXME use exam_tag_id
		DB::statement("UPDATE users SET has_finished_entry_exam = 1 WHERE created_at < '2019-03-30 00:00:00' OR id IN (SELECT exams_results.user_id FROM exams_results JOIN orders ON orders.user_id = exams_results.user_id WHERE orders.paid = 1 AND orders.product_id = 13 AND exams_results.created_at > '2019-04-01')");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("UPDATE users SET has_finished_entry_exam = 0;");
	}
}
