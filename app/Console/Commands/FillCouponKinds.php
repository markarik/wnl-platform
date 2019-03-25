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
	 * @return mixed
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
			}
			// I didn't set "voucher" kind on coupons on purpose because:
			// 1. It won't cause any problems that this kind is not set,
			// 2. When creating vouchers user can pass different arguments like name, value, type etc,
			// hence, it's hard to estimate if it's really a voucher basing only on historical data

			$coupon->save();
			$bar->advance();
		}

		$bar->finish();
		print PHP_EOL;
	}
}
