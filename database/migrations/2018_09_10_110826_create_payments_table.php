<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function (Blueprint $table) {
			$table->increments('id');
			$table->enum('status', [
				'in-progress',
				'success',
				'error'
			]);
			$table->unsignedInteger('external_id')->nullable();
			$table->unsignedInteger('order_id')->index();
			$table->float('amount')->nullable();
			$table->string('session_id')->unique()->nullable();
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
		Schema::dropIfExists('payments');
	}
}
