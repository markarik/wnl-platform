<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanupLessonsAndGroupsAfterNewStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
			Schema::table('lessons', function (Blueprint $table) {
				$table->dropColumn('order_number');
				$table->dropColumn('group_id');
			});
			Schema::table('groups', function (Blueprint $table) {
				$table->dropColumn('order_number');
			});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration is irreversible
    }
}
