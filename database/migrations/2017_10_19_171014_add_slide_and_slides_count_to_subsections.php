<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlideAndSlidesCountToSubsections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subsections', function (Blueprint $table) {
			$table->integer('first_slide')->nullable()->after('section_id');
			$table->integer('slides_count')->nullable()->after('first_slide');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subsections', function (Blueprint $table) {
			$table->dropColumn('first_slide');
			$table->dropColumn('slides_count');
        });
    }
}
