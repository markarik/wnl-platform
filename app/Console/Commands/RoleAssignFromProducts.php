<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class RoleAssignFromProducts extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'role:assignFromProducts {role} {products}';

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
		$role = Role::where('name', $this->argument('role'))->first();
		if (!$role) {
			$this->warn('Invalid role name');

			return 42;
		}

		$products = $this->argument('products');

		$orders = Order::whereIn('product_id', explode(',', $products))->where('paid', 1)->get();

		if ($orders->count() === 0) {
			$this->warn('No orders found. Make sure second argument is whether a comma separated list of actual products.');
			return 42;
		}

		$i = 0;

		foreach ($orders as $order) {
			$user = User::find($order->user_id);
			if (!$user->roles->contains($role)) {
				$user->roles()->attach($role);
				$i++;
			}
		}

		$this->info("Assigned role to {$i} users.");
	}
}
