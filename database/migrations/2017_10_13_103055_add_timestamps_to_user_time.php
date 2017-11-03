<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToUserTime extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_time', function (Blueprint $table) {
			$table->timestamps();
			$table->increments('id')->first('user_id');
			$table->unique(
				[
					'user_id',
					'created_at',
				],
				'user_time_per_day'
			);

			$table->dropUnique('user_time_user_id_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_time', function (Blueprint $table) {
			$table->dropTimestamps();
			$table->dropColumn(['id']);
			$table->dropUnique('user_time_per_day');
		});
 	}
}
