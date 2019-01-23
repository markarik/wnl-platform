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
	        $table->unsignedInteger('course_id');
	        $table->morphs('structurable');
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
