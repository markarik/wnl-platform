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
		DB::statement("UPDATE users SET has_finished_entry_exam = 1 WHERE created_at < '2019-03-30 00:00:00'");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("UPDATE users SET has_finished_entry_exam = 0 WHERE created_at < '2019-03-30 00:00:00'");
	}
}
