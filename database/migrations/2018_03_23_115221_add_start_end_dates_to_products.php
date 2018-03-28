<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartEndDatesToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->timestamp('course_start')->nullable();
            $table->timestamp('course_end')->nullable();
            $table->timestamp('access_start')->nullable();
            $table->timestamp('access_end')->nullable();
            $table->timestamp('signups_start')->nullable();
            $table->timestamp('signups_end')->nullable();
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
			$table->timestamp('start_date')->nullable()->after('delivery_date');
			$table->timestamp('end_date')->nullable()->after('start_date');
			$table->dropColumn('course_start');
			$table->dropColumn('course_end');
			$table->dropColumn('access_start');
			$table->dropColumn('access_end');
			$table->dropColumn('signups_start');
			$table->dropColumn('signups_end');
        });
    }
}
