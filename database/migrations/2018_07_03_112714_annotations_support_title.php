<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnnotationsSupportTitle extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('annotations', function (Blueprint $table) {
			$table->renameColumn('keyword', 'title');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('annotations', function (Blueprint $table) {
			$table->renameColumn('title', 'keyword');
		});
	}
}
