<?php

namespace App\Console\Commands;

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
    protected $signature = 'invoice:correct {order}';

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

		$reason = $this->ask('Reason');
		$value = $this->ask('Refunded amount');
		$invoice = (new Invoice)->corrective($order, $corrected, $reason, -$value);
		$order->paid_amount -= $value;
		$order->save();

		Mail::to($order->user)->send(new Refund($order, $invoice, $value));

		$this->info('OK.');

		return 42;
	}
}
