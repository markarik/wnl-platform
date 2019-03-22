<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Console\Command;

class CouponsAttachProducts extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'coupons:attachProducts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$coupons = Coupon::all();

		$online = Product::slug(Product::SLUG_WNL_ONLINE);

		foreach ($coupons as $coupon) {
			$coupon->products()->attach($online);
		}
	}
}
