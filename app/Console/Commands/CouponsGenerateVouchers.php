<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CouponsGenerateVouchers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'coupons:generateVouchers';

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
		$name = $this->ask('Coupon name');
		$type = $this->ask('Type (percentage / amount)');
		$value = $this->ask('Value');
		$count = $this->ask('Number of coupons');
		$now = Carbon::now();
		$coupons = [];

		$this->info("name \t type \t code \t value");

		for ($i = 0; $i < $count; $i++) {
			$code = $this->randomCode();
			print "{$name}\t{$type}\t{$code}\t$value\n";

			$coupons[] = [
				'name'         => $name,
				'code'         => $code,
				'type'         => $type,
				'value'        => $value,
				'expires_at'   => '2028-01-01',
				'times_usable' => 1,
				'created_at'   => $now,
				'updated_at'   => $now,
			];
		}

		print "\n";


		foreach ($coupons as $coupon) {
			Coupon::create($coupon);
		}

		return;
	}

	protected function randomCode()
	{
		return strtoupper(str_random(6));
	}
}
