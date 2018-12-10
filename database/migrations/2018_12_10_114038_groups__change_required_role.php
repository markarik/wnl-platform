<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GroupsChangeRequiredRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
			$table->unsignedInteger('required_role_id')->nullable();
        });

		// Currently we use only one role in this column so we can simplify the migration
		DB::table('groups')
			->whereNotNull('required_role')
			->update([
				"required_role_id" => 3
			]);

        Schema::table('groups', function (Blueprint $table) {
			$table->dropColumn('required_role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('groups', function (Blueprint $table) {
			$table->string('required_role')->nullable();
		});

		// Currently we use only one role in this column so we can simplify the migration
		DB::table('groups')
			->whereNotNull('required_role_id')
			->update([
				"required_role" => 'workshop-participant'
			]);

		Schema::table('groups', function (Blueprint $table) {
			$table->dropColumn('required_role_id');
		});
    }
}
