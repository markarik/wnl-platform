<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('user_progress', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('lesson_id');
			$table->unsignedInteger('user_id');
			$table->unsignedInteger('edition_id');
			$table->string('route');
			$table->enum('status', [
				config('lessons.progress.in_progress'),
				config('lessons.progress.done'),
			]);
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
		Schema::dropIfExists('user_progress');
    }
}
