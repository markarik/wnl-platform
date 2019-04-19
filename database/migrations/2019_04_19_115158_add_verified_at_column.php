<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerifiedAtColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('comments', function (Blueprint $table) {
			$table->timestamp('verified_at')->nullable();
		});
		Schema::table('qna_questions', function (Blueprint $table) {
			$table->timestamp('verified_at')->nullable();
		});
		Schema::table('qna_answers', function (Blueprint $table) {
			$table->timestamp('verified_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('comments', function (Blueprint $table) {
			$table->dropColumn('verified_at');
		});
		Schema::table('qna_questions', function (Blueprint $table) {
			$table->dropColumn('verified_at');
		});
		Schema::table('qna_answers', function (Blueprint $table) {
			$table->dropColumn('verified_at');
		});
	}
}
