<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UniquePresentables extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('presentables', function (Blueprint $table) {
			$table->unique(['presentable_type', 'presentable_id', 'slide_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('presentables', function (Blueprint $table) {
			$table->dropUnique(['presentable_type', 'presentable_id', 'slide_id']);
		});
	}
}
