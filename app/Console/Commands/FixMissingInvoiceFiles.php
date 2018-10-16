<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class FixMissingInvoiceFiles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoices:fixMissing';

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
		$this->info('Looking for missing invoice files ...');
		$invoices = Invoice::all();
		$count = $invoices->count();

		$bar = $this->output->createProgressBar($count);
		$regenerated = 0;
		foreach ($invoices as $invoice) {
			if (!\Storage::exists($invoice->file_path) && $invoice->meta) {
				$this->call('invoice:render', ['invoiceId' => $invoice->id]);
				$regenerated++;
			}
			$bar->advance();
		}

		$bar->finish();
		$this->info('Done. Regenerated ' . $regenerated . ' files.');
		return;
	}
}
