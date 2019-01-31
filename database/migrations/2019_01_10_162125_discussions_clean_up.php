<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DiscussionsCleanUp extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Clean up data, there is no way to rollback this
		DB::statement('DELETE FROM qna_questions WHERE discussion_id IS NULL;');
		DB::statement('DELETE FROM comments WHERE commentable_type = \'App\\Models\\QnaAnswer\' AND commentable_id IN (SELECT qna_answers.id FROM qna_answers LEFT JOIN qna_questions ON qna_questions.id=qna_answers.question_id WHERE qna_questions.id IS NULL);');
		DB::statement('DELETE FROM qna_answers WHERE id IN (SELECT tmp.id FROM (SELECT * FROM qna_answers) AS tmp LEFT JOIN qna_questions ON qna_questions.id=tmp.question_id WHERE qna_questions.id IS NULL);');

		// 'Prezentacja' tag
		DB::statement('DELETE FROM taggables WHERE tag_id = 8;');
		DB::statement('DELETE FROM tags WHERE id = 8;');

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
