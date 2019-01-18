<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseStructureNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_structure_nodes', function (Blueprint $table) {
	        $table->increments('id');
	        // TODO: discussion trigger - add here course_id ? Or maybe structure_id, as there may be many structures within a course?
	        $table->string('name');
	        $table->string('type');
	        $table->nestedSet();
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_structure_nodes');
    }
}
