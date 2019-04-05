<?php

use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersCanceledNotNullable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Order::query()->update(array('canceled' => 0));
		// We cannot modify table that conains enum
		DB::statement('ALTER TABLE orders CHANGE canceled canceled tinyint(4) NOT NULL default 0;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
			// Don't reverse this migration - this was a bug
	}
}
