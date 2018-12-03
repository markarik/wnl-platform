<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SiteWideMessagesForDashboardNews extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('site_wide_messages', function (Blueprint $table) {
			// Laravel doesn't support migration on a table with ENUM column https://stackoverflow.com/a/33142304
			DB::statement('ALTER TABLE site_wide_messages CHANGE COLUMN user_id user_id INT');
			DB::statement('ALTER TABLE site_wide_messages CHANGE COLUMN start_date start_date DATETIME');
			DB::statement('ALTER TABLE site_wide_messages CHANGE COLUMN end_date end_date DATETIME');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('site_wide_messages', function (Blueprint $table) {
			DB::statement('ALTER TABLE site_wide_messages CHANGE COLUMN user_id user_id INT NOT NULL');
			DB::statement('ALTER TABLE site_wide_messages CHANGE COLUMN start_date start_date DATETIME NOT NULL');
			DB::statement('ALTER TABLE site_wide_messages CHANGE COLUMN end_date end_date DATETIME NOT NULL');
		});
	}
}
