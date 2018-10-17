<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class MigrateInvoiceType extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoices:migrateType';

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

	const TYPE_SERIES_MAP = [
		'PROFORMA' => 'pro-forma',
		'F-ZAL' => 'advance',
		'FK' => 'final',
		'KOR' => 'corrective',
		'FV' => 'vat',
		'KOR/RE' => 'corrective',
	];

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$invoices = Invoice::all();
		$count = $invoices->count();

		$bar = $this->output->createProgressBar($count);
		foreach ($invoices as $invoice) {
			$invoice->type = self::TYPE_SERIES_MAP[$invoice->series];
			$invoice->save();
			$bar->advance();
		}
		$bar->finish();
		print PHP_EOL;

		$this->info('Done.');

		return;
	}
}
