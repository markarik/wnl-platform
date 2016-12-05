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
			$table->string('name');
			$table->unsignedInteger('chapter_id');
			$table->timestamps();

			$table->foreign('chapter_id')
				->references('id')
				->on('chapters');
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
