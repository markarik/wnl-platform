<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxonomyTermablesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('taxonomy_termables', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('taxonomy_term_id');
			$table->unsignedInteger('taxonomy_termable_id');
			$table->string('taxonomy_termable_type');
			$table->unique(
				['taxonomy_term_id', 'taxonomy_termable_id', 'taxonomy_termable_type'],
				'taxonomy_termable_unique'
			);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('taxonomy_termables');
	}
}
