<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class FillCouponKinds extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'data-migration:coupon-fill-kinds';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fill the coupon kind column based on coupon data.';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$coupons = Coupon::all();
		$bar = $this->output->createProgressBar($coupons->count());

		foreach ($coupons as $coupon) {
			if ($coupon->name === 'Study Buddy') {
				$coupon->kind = Coupon::KIND_STUDY_BUDDY;
			} else if ($coupon->name === 'Zniżka -50% na Kurs internetowy dla uczestników poprzednich edycji kursu Więcej niż LEK') {
				$coupon->kind = Coupon::KIND_PARTICIPANT;
			} else if ($coupon->name === 'Zniżka grupowa') {
				$coupon->kind = Coupon::KIND_GROUP;
			} else {
				$coupon->kind = Coupon::KIND_VOUCHER;
			}

			$coupon->save();
			$bar->advance();
		}

		$bar->finish();
		print PHP_EOL;
	}
}
