<?php

namespace App\Console\Commands;

use App\Mail\CorrectiveInvoice;
use App\Mail\Refund;
use App\Models\Invoice as InvoiceModel;
use Lib\Invoice\Invoice;
use App\Models\Order;
use Illuminate\Console\Command;
use Mail;

class InvoiceCorrect extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoice:correct {order} {--no-refund}';

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
		$orderId = $this->argument('order');
		$order = Order::find($orderId);

		if (!$order) {
			$this->warn('Order doesn\'t exist.');

			return 42;
		}

		if (count($order->invoices()->get()) > 2 &&
			!$this->confirm('This order has suspiciously many invoices... Do you want to continue?')
		) {
			$this->info('Aborted.');
			exit;
		}

		$this->correct($order);

		return 42;
	}

	protected function correct($order)
	{
		foreach ($order->invoices as $invoice) {
			$this->info("({$invoice->id}) {$invoice->full_number} | {$invoice->amount}PLN");
		}
		$invoiceId = $this->ask('ID of invoice to be corrected');
		$this->info("Do you want to refund this order:");
		$this->table(
			['id', 'email', 'name', 'product',],
			[[$order->id, $order->user->email, $order->user->full_name, $order->product->name]]
		);

		if (!$this->confirm("Please confirm")) {
			$this->info('Aborted.');
			exit;
		}

		$corrected = $order->invoices()->find($invoiceId);
		if (!$corrected) {
			$this->warn('Invoice doesn\'t exist.');

			return 42;
		}

		$reason = $this->ask('Reason', 'Zwrot na podstawie rabatu naliczonego po opłaceniu zamówienia.');
		$value = $this->ask('Amount');
		$refund = !$this->option('no-refund');
		$invoice = (new Invoice)->corrective($order, $corrected, $reason, -$value, $refund);
		$order->paid_amount -= $value;
		$order->save();

		if ($refund) {
			$mail = new Refund($order, $invoice, $value);
		} else {
			$mail = new CorrectiveInvoice($order, $invoice, $value);
		}

		Mail::to($order->user)->send($mail);

		$this->info('OK. Invoice number: ' . $invoice->full_number);

		return 42;
	}
}
