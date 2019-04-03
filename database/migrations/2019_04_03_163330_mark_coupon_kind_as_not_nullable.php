<?php

use Illuminate\Database\Migrations\Migration;

class MarkCouponKindAsNotNullable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Laravel doesn't support migration on a table with ENUM column https://stackoverflow.com/a/33142304
		DB::statement("ALTER TABLE coupons CHANGE COLUMN kind kind enum('group', 'voucher', 'participant', 'study_buddy') NOT NULL");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE coupons MODIFY COLUMN kind enum('group', 'voucher', 'participant', 'study_buddy')");
	}
}
