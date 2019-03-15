<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class GenerateCouponsForUsers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'coupons:generate {productIds*}';

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
		$productIds = $this->argument('productIds');

		/** @var User[]|Collection $users */
		$users = User::select('id')
			->whereHas('orders', function ($query) use ($productIds){
				$query->where('paid', 1);
				$query->whereIn('product_id', $productIds);
			})
			->where('suspended', 0)
			->get();

		if (!$this->confirm($users->count() . ' users will receive a 50% coupon. Continue?')) die;

		$expires = Carbon::now()->addYears(10);

		foreach ($users as $user) {
			// We don't want to dispatch events and sync coupons for participants from previous edition
			Coupon::flushEventListeners();
			Coupon::create([
				'user_id'    => $user->id,
				'name'       => 'Zniżka -50% na Kurs internetowy dla uczestników poprzednich edycji kursu Więcej niż LEK',
				'type'       => 'percentage',
				'slug'       => 'wnl-online-only',
				'value'      => 50,
				'expires_at' => $expires,
				'kind' => Coupon::KIND_PARTICIPANT,
			]);
		}

		\Artisan::call('coupons:attachProducts');
		return;
	}
}
