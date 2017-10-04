<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudyBuddyTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('study_buddy', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('order_id')->index();
			$table->string('code')->index();
			$table->string('recipient')->nullable();
			$table->string('status')->default('new');
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
		Schema::dropIfExists('study_buddy');
	}
}
