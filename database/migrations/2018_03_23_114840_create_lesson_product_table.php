<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_product', function (Blueprint $table) {
            $table->unsignedInteger('lesson_id')->index();
            $table->unsignedInteger('product_id')->index();
            $table->timestamp('start_date')->nullable();
            $table->unique(['lesson_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_product');
    }
}
