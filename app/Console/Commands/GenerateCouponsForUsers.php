<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateCouponsForUsers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'coupons:generate';

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
		$users = User::select('id')
			->whereHas('orders', function ($query) {
				$query->where('paid', 1);
			})
			->whereHas('roles', function ($query) {
				$query->where('name', 'edition-2-participant');
			})
			->where('suspended', 0)
			->get();

		$expires = Carbon::now()->addYears(10);
		foreach ($users as $user) {
			Coupon::create([
				'user_id'    => $user->id,
				'name'       => 'Zniżka -50% na Kurs internetowy dla uczestników poprzednich edycji kursu Więcej niż LEK',
				'type'       => 'percentage',
				'slug'       => 'wnl-online-only',
				'value'      => 50,
				'expires_at' => $expires,
			]);
		}

		return;
	}
}
