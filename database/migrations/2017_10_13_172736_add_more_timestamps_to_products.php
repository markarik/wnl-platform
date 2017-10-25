<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreTimestampsToProducts extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('products', function (Blueprint $table) {
			$table->timestamp('delivery_date')->nullable()->after('initial');
			$table->timestamp('start_date')->nullable()->after('delivery_date');
			$table->timestamp('end_date')->nullable()->after('start_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('products', function (Blueprint $table) {
			$table->dropColumn('delivery_date');
			$table->dropColumn('start_date');
			$table->dropColumn('end_date');
		});
	}
}
