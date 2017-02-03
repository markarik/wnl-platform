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
			$table->increments('id');
			$table->unsignedInteger('subject_id');
			$table->string('name');
			$table->unsignedInteger('slide_id');
			$table->timestamps();

			$table
				->foreign('subject_id')
				->references('id')
				->on('subjects');

			$table
				->foreign('slide_id')
				->references('id')
				->on('slides');
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
