<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CourseAddIsPlanBuilderEnabled extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
			Schema::table('courses', function (Blueprint $table) {
				$table->tinyInteger('is_plan_builder_enabled')->default(1)->after('slug');
			});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
			Schema::table('courses', function (Blueprint $table) {
				$table->dropColumn('is_plan_builder_enabled');
			});
	}
}
