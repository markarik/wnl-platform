<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReactablesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reactables', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('reaction_id');
			$table->morphs('reactable');
			$table->json('context')->nullable();
			$table->timestamps();

			$table->unique(
				[
					'user_id',
					'reaction_id',
					'reactable_id',
					'reactable_type',
				],
				'unique_reaction'
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
		Schema::dropIfExists('reactables');
	}
}
