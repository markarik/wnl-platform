<?php

use Illuminate\Database\Migrations\Migration;

class MarkOnboardingAsFinishedForOldProducts extends Migration
{
	const PRODUCTS_IDS = [11, 12];

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement(
			"INSERT INTO user_product_states (user_id, product_id, onboarding_step) SELECT user_id, product_id, 'finished' FROM orders WHERE orders.paid = 1 AND orders.product_id IN (" .
			implode(',', self::PRODUCTS_IDS) .
			") ON DUPLICATE KEY UPDATE onboarding_step = 'finished';",
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement(
			"DELETE FROM user_product_states WHERE product_id IN (" .
			implode(',', self::PRODUCTS_IDS) .
			")",
		);
	}
}
