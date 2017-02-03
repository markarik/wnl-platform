<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('sections', function (Blueprint $table) {
			$table->unsignedInteger('id');
			$table->unsignedInteger('subject_id');
			$table->string('name');
			$table->unsignedInteger('slide_id');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('sections');
    }
}
