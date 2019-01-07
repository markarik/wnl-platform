<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QnaQuestionDiscussionIdNotNullable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('qna_questions', function (Blueprint $table) {
			DB::statement('ALTER TABLE qna_questions MODIFY discussion_id INTEGER NOT NULL');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('qna_questions', function (Blueprint $table) {
			DB::statement('ALTER TABLE qna_questions MODIFY discussion_id INTEGER NULL');
		});
	}
}
