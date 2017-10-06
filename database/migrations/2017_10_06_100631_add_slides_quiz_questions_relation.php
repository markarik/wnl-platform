<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlidesQuizQuestionsRelation extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	 public function up()
	 {
		 Schema::create('slide_quiz_question', function (Blueprint $table) {
			 $table->increments('id');
			 $table->unsignedInteger('slide_id');
			 $table->unsignedInteger('quiz_question_id');
		 });
	 }

	 /**
	  * Reverse the migrations.
	  *
	  * @return void
	  */
	 public function down()
	 {
		 Schema::dropIfExists('slide_quiz_question');
	 }
}
