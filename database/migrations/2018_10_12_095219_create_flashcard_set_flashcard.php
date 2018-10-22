<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlashcardSetFlashcard extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('flashcards_set_flashcard', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('flashcard_set_id');
			$table->integer('flashcard_id');
			$table->integer('order_number');
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
		Schema::dropIfExists('flashcards_set_flashcard');
	}
}
