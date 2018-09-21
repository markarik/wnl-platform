<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\EditionsApiController;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Cache;

class MigrateUserSubscription extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:migrate-subscription {--admins}';

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

		if ($this->option('admins')) {
			$users = User::all()->filter(function ($user) {
				return $user->isAdmin();
			});
			$dates = new \stdClass();
			$dates->min = Carbon::now()->subYear();
			$dates->max = Carbon::now()->addYears(10);
		} else {
			$users = User::all();
		}

		foreach($users as $user) {
			$dates = $dates ?? \DB::table('orders')
				->selectRaw('max(products.access_end) as max, min(products.access_start) as min')
				->join('products', 'orders.product_id', '=', 'products.id')
				->where('orders.user_id', $user->id)
				->where('orders.paid', 1)
				->where('orders.canceled', '<>', 1)
				->first();

			if ($dates->min && $dates->max) {
				UserSubscription::updateOrCreate(
					['user_id' => $user->id],
					['access_start' => $dates->min, 'access_end' => $dates->max]
				);
			}

			\Cache::forget(EditionsApiController::key($user->id));
		}
	}
}
