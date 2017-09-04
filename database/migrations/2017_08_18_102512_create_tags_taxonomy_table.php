<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTaxonomyTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tags_taxonomy', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('tag_id');
			$table->integer('parent_tag_id')->nullable();
			$table->integer('taxonomy_id');
			$table->integer('order_number')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tags_taxonomy');
	}
}
