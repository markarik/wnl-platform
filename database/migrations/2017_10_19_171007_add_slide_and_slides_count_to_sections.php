<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlideAndSlidesCountToSections extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sections', function (Blueprint $table) {
			$table->integer('first_slide')->nullable()->after('screen_id');
			$table->integer('slides_count')->nullable()->after('first_slide');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sections', function (Blueprint $table) {
			$table->dropColumn('first_slide');
			$table->dropColumn('slides_count');
		});
	}
}
