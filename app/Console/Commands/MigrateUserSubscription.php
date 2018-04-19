<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Console\Command;

class MigrateUserSubscription extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:migrate-subscription';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Assign subscription to a user base on order history.';

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

		$users = User::all();

		foreach($users as $user) {
			$dates = \DB::table('orders')
				->selectRaw('max(products.access_end) as max, min(products.access_start) as min')
				->join('products', 'orders.product_id', '=', 'products.id')
				->where('orders.user_id', $user->id)
				->where('orders.paid', 1)
				->where('orders.canceled', '<>', 1)
				->first();

			if ($dates->min && $dates->max) {
				$subscription = UserSubscription::updateOrCreate(
					['user_id' => $user->id],
					['access_start' => $dates->min, 'access_end' => $dates->max]
				);
			}
		}
	}
}
