<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionAndColorToTags extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('tags', function (Blueprint $table) {
			$table->string('description', 510)->nullable();
			$table->string('color', 6)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('tags', function (Blueprint $table) {
			$table->removeColumn('description');
			$table->removeColumn('color');
		});
	}
}
