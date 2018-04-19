<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OrderCancelAllUnpaid extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'orders:cancelAllUnpaid {--until=}';

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
		if (!$this->option('until')) {
			$this->warn('No date limit specified. All unpaid orders will'
				. 'be canceled. Use --until option to specify date limit.');
		}

		$until = Carbon::parse($this->option('until'));

		$orders = Order::where('paid', 0)
			->where('created_at', '<', $until)
			->where('canceled', '!=', 1)
			->get();

		if (!$this->confirm("Cancel {$orders->count()} orders?")) {
			$this->info('Aborted.');
			exit;
		}

		foreach ($orders as $order) {
			$order->cancel();
		}

		$this->info('It is done my Lord.');
	}
}
