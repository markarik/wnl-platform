<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePresentablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('presentables', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('slide_id');
			$table->unsignedInteger('presentable_id');
			$table->string('presentable_type');
			$table->timestamps();

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
		Schema::drop('presentables');
    }
}
