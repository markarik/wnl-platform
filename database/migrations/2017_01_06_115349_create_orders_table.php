<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('orders', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('product_id');
			$table->string('method')->nullable();
			$table->tinyInteger('paid')->default(0);
            $table->string('session_id')->unique()->nullable();
            $table->string('external_id')->unique()->nullable();
            $table->string('transfer_title')->unique()->nullable();
            $table->tinyInteger('invoice')->default(0);
            $table->string('invoice_name')->nullable();
            $table->string('invoice_nip')->nullable();
            $table->string('invoice_address')->nullable();
            $table->string('invoice_zip')->nullable();
            $table->string('invoice_city')->nullable();
            $table->string('invoice_country')->nullable();
            $table->tinyInteger('consent_newsletter')->nullable();
			$table->timestamps();

			$table->foreign('user_id')
				->references('id')
				->on('users');

			$table->foreign('product_id')
				->references('id')
				->on('products');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('orders');
    }
}
