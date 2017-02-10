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
			$table->unsignedInteger('structure_id');
			$table->unsignedInteger('snippet_id')->nullable();
			$table->timestamps();

			$table
				->foreign('structure_id')
				->references('id')
				->on('structures')
				->onDelete('cascade');

			$table
				->foreign('snippet_id')
				->references('id')
				->on('snippets')
				->onDelete('cascade');
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
