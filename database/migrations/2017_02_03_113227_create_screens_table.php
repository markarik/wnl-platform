<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScreensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('screens', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('lesson_id');
			$table->unsignedInteger('snippet_id');
			$table->timestamps();

			$table
				->foreign('lesson_id')
				->references('id')
				->on('lessons');

			$table
				->foreign('snippet_id')
				->references('id')
				->on('snippets');
		});
    }
	/**

	 * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('screens');
    }
}
