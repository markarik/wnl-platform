<?php

use Illuminate\Database\Migrations\Migration;

class DeleteOptionalUserLessons extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('DELETE FROM user_lesson WHERE lesson_id IN (SELECT id FROM lessons WHERE is_required = 0);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// This migration is not reversible
	}
}
