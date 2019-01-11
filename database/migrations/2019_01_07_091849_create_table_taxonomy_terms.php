<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTaxonomyTerms extends Migration
{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up()
		{
			Schema::create('taxonomy_terms', function (Blueprint $table) {
				$table->increments('id');
				$table->nestedSet();
				$table->integer('tag_id')->index();
				$table->integer('taxonomy_id')->index();
				$table->string('description', 1000)->nullable();
				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down()
		{
			Schema::dropIfExists('taxonomy_terms');
		}
}
