<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class InvoiceRender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:render {invoiceId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PDF file for given invoice.';

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
        $invoice = Invoice::find($this->argument('invoiceId'));

        if (!$invoice) {
	        $this->warn('Invoice not found.');
	        die;
        }

        (new \Lib\Invoice\Invoice)->renderAndSave('payment.invoices.' . $invoice->type, $invoice->meta);

        return;
    }
}
