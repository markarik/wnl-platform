<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Class RecreateCourseStructure
 */
class RecreateCourseStructure extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('course_structure_nodes')->truncate();
		// First version of the script messed up the ordering, let's recreate the structure with a fixed script
		\Artisan::call('data-migration:create-nested-course-structure');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
}
