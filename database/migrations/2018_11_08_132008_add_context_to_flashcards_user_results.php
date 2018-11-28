<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContextToFlashcardsUserResults extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_flashcards_results', function (Blueprint $table) {
			$table->nullableMorphs('context');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_flashcards_results', function (Blueprint $table) {
			$table->dropColumn('context_type');
			$table->dropColumn('context_id');
		});
	}
}
