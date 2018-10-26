<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteWideMessagesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_wide_messages', function (Blueprint $table) {
			$table->increments('id');
			$table->text('message');
			$table->dateTime('start_date');
			$table->dateTime('end_date');
			$table->enum('target', [
				'dashboard-news',
				'site-wide-alert'
			]);
			$table->dateTime('read_at')->nullable();
			$table->integer('user_id')->index();
			$table->string('slug')->nullable();
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
		Schema::dropIfExists('site_wide_messages');
	}
}
