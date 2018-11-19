<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FlashcardsIndexes extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('flashcards_set_flashcard', function (Blueprint $table) {
			$table->index('flashcard_set_id');
			$table->index('flashcard_id');
		});

		Schema::table('user_flashcards_results', function (Blueprint $table) {
			$table->index('user_id');
			$table->index('flashcard_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('flashcards_set_flashcard', function (Blueprint $table) {
			$table->dropIndex(['flashcard_set_id']);
			$table->dropIndex(['flashcard_id']);
		});

		Schema::table('user_flashcards_results', function (Blueprint $table) {
			$table->dropIndex(['user_id']);
			$table->dropIndex(['flashcard_id']);
		});
	}
}
