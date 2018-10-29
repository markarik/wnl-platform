<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFlashcardResultsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_flashcards_results', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('flashcard_id');
			$table->enum('answer', [
				'easy', 'hard', 'do not know'
			]);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_flashcards_results');
	}
}
