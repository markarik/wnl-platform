<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTaggablesUniqueTaggableCombo extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('taggables', function (Blueprint $table) {
			$table->unique(['taggable_type', 'taggable_id', 'tag_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('taggables', function (Blueprint $table) {
			$table->dropUnique(['taggable_type', 'taggable_id', 'tag_id']);
		});
	}
}
