<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkPaymentsAsFailed extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'payments:markFailed {paymentId?}';

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
		$paymentId = $this->argument('paymentId');

		if (!empty($paymentId)) {
			$payment = Payment::find($paymentId);
			if (empty($payment)) {
				return $this->warn('Invalid payment id');
			}
			$payment->status = 'error';
			$payment->save();
			return $this->info('Done');
		}

		$payments = Payment
			::where('created_at', '<', Carbon::now()->subMinutes(60))
			->whereNotIn('status', ['success', 'error'])
			->get();

		$bar = $this->output->createProgressBar($payments->count());
		foreach ($payments as $payment) {
			$payment->status = 'error';
			$payment->save();
			$bar->advance();
		}

		$bar->finish();
		return $this->info('Done');
	}
}
