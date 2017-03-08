<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonAvailabilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('lesson_availability', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('edition_id');
			$table->unsignedInteger('lesson_id');
			$table->timestamp('start_date');
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
		Schema::dropIfExists('lesson_availability');
    }
}
