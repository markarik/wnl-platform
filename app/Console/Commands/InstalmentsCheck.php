<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class InstalmentsCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instalments:check';

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
        $orders = Order::where('method', 'instalments')
			->whereIn('product_id', [9,10])
			->get();

		foreach ($orders as $order) {
			if (!$this->check($order)) {
				$this->warn("Whooooops, order {$order->id}");
//				die;
			}
        }

        $this->info('Done.');

        return;
    }

    protected function check($order) {
		if ($order->instalments['allPaid']) {
			return
				$order->orderInstalments->sum('paid_amount')
				=== $order->total_with_coupon;
		}

		foreach ($order->orderInstalments as $orderInstalment) {
			$instalment = $order->instalments['instalments'][$orderInstalment->order_number -1];
			if ($instalment['amount'] !== $orderInstalment->amount) {
				return false;
			}

			$paid = $instalment['amount'] - $instalment['left'];
			if ($paid !== $orderInstalment->paid_amount) {
				return false;
			}
    	}

    	return true;
	}
}
