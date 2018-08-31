<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeys extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users_plan_progress', function (Blueprint $table) {
			$table->index('user_id');
			$table->index('plan_id');
			$table->index('question_id');
		});

		Schema::table('users_plans', function (Blueprint $table) {
			$table->index('user_id');
		});

		Schema::table('user_time', function (Blueprint $table) {
			$table->index('user_id');
		});

		Schema::table('user_quiz_results', function (Blueprint $table) {
			$table->index('quiz_question_id');
			$table->index('quiz_answer_id');
		});

		Schema::table('user_progress', function (Blueprint $table) {
			$table->index('lesson_id');
			$table->index('user_id');
			$table->index('edition_id');
		});

		Schema::table('user_profiles', function (Blueprint $table) {
			$table->index('user_id');
		});

		Schema::table('user_course_progress', function (Blueprint $table) {
			$table->index('user_id');
			$table->index('lesson_id');
		});

		Schema::table('user_billing_data', function (Blueprint $table) {
			$table->index('user_id');
		});

		Schema::table('user_addresses', function (Blueprint $table) {
			$table->index('user_id');
		});

		Schema::table('tags_taxonomy', function (Blueprint $table) {
			$table->index('tag_id');
			$table->index('parent_tag_id');
			$table->index('taxonomy_id');
		});

		Schema::table('taggables', function (Blueprint $table) {
			$table->index('tag_id');
			$table->index('taggable_id');
		});

		Schema::table('sessions', function (Blueprint $table) {
			$table->index('user_id');
		});

		Schema::table('sections', function (Blueprint $table) {
			$table->index('screen_id');
		});

		Schema::table('role_user', function (Blueprint $table) {
			$table->index('role_id');
			$table->index('user_id');
		});

		Schema::table('reactables', function (Blueprint $table) {
			$table->index('reaction_id');
		});

		Schema::table('quiz_question_quiz_set', function (Blueprint $table) {
			$table->index('quiz_question_id');
			$table->index('quiz_set_id');
		});

		Schema::table('quiz_answers', function (Blueprint $table) {
			$table->index('quiz_question_id');
		});

		Schema::table('qna_questions', function (Blueprint $table) {
			$table->index('user_id');
		});

		Schema::table('presentables', function (Blueprint $table) {
			$table->index('slide_id');
			$table->index('presentable_id');
		});

		Schema::table('notifications', function (Blueprint $table) {
			$table->index('event_id');
		});

		Schema::table('lesson_availabilities', function (Blueprint $table) {
			$table->index('edition_id');
			$table->index('lesson_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users_plan_progress', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
			$table->dropIndex(['plan_id']);
			$table->dropIndex(['question_id']);
		});

		Schema::table('users_plans', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
		});

		Schema::table('user_time', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
		});

		Schema::table('user_quiz_results', function (Blueprint $table) {
			$table->dropIndex(['quiz_question_id']);
			$table->dropIndex(['quiz_answer_id']);
		});

		Schema::table('user_progress', function (Blueprint $table) {
			$table->dropIndex(['lesson_id']);
			$table->dropIndex(['user_id']);
			$table->dropIndex(['edition_id']);
		});

		Schema::table('user_profiles', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
		});

		Schema::table('user_course_progress', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
			$table->dropIndex(['lesson_id']);
		});

		Schema::table('user_billing_data', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
		});

		Schema::table('user_addresses', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
		});

		Schema::table('tags_taxonomy', function (Blueprint $table) {
			$table->dropIndex(['tag_id']);
			$table->dropIndex(['parent_tag_id']);
			$table->dropIndex(['taxonomy_id']);
		});

		Schema::table('taggables', function (Blueprint $table) {
			$table->dropIndex(['tag_id']);
			$table->dropIndex(['taggable_id']);
		});

		Schema::table('sessions', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
		});

		Schema::table('sections', function (Blueprint $table) {
			$table->dropIndex(['screen_id']);
		});

		Schema::table('role_user', function (Blueprint $table) {
			$table->dropIndex(['role_id']);
			$table->dropIndex(['user_id']);
		});

		Schema::table('reactables', function (Blueprint $table) {
			$table->dropIndex(['reaction_id']);
		});

		Schema::table('quiz_question_quiz_set', function (Blueprint $table) {
			$table->dropIndex(['quiz_question_id']);
			$table->dropIndex(['quiz_set_id']);
		});

		Schema::table('quiz_answers', function (Blueprint $table) {
			$table->dropIndex(['quiz_question_id']);
		});

		Schema::table('qna_questions', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
		});

		Schema::table('presentables', function (Blueprint $table) {
			$table->dropIndex(['slide_id']);
			$table->dropIndex(['presentable_id']);
		});

		Schema::table('notifications', function (Blueprint $table) {
			$table->dropIndex(['event_id']);
		});

		Schema::table('lesson_availabilities', function (Blueprint $table) {
			$table->dropIndex(['edition_id']);
			$table->dropIndex(['lesson_id']);
		});
	}
}
