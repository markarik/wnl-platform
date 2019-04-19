<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceChangelogTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('resource_changelog', function (Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->morphs('resource');
			$table->string('property');
			$table->string('value')->nullable();
			// user_id can be null because some changes may be done outside of the request context
			// for instance artisan command
			$table->integer('user_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('resource_changelog');
	}
}
