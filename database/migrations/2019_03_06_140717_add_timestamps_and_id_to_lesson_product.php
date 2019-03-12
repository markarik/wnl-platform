<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsAndIdToLessonProduct extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('lesson_product', function (Blueprint $table) {
			$table->increments('id')->first();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('lesson_product', function (Blueprint $table) {
			$table->dropColumn('id');
			$table->dropTimestamps();
		});
	}
}
