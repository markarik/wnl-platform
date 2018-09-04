<?php

namespace App\Console\Commands;

use App\Jobs\CreateGroupDiscount;
use App\Models\Product;
use Illuminate\Console\Command;

class CouponsGenerateGroup extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'coupons:generateGroup {emails}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
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
		$couponAttributes = [
			'name' => $this->ask('Coupon name', 'Zniżka grupowa'),
			'type' => $this->anticipate('Type', ['percentage', 'amount'], 'percentage'),
			'value' => $this->ask('Value', 10),
			'expires' => $this->ask('Expiration date', Product::max('signups_end')),
		];

		$emails = $this->argument('emails');

		$mailCollection = collect(explode("\n", $emails))
			->reject(function ($value) {
				return empty($value);
			});

		dispatch(new CreateGroupDiscount($mailCollection, $couponAttributes));
		return;
	}
}
