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
			$table->unsignedInteger('id');
			$table->unsignedInteger('lesson_id');
			$table->unsignedInteger('snippet_id');
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
