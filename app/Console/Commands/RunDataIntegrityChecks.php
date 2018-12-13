<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Tests\DataIntegrity\PaymentsCheck;
use Tests\DataIntegrity\PresentablesOrderNumberCheck;

class RunDataIntegrityChecks extends Command
{
	protected $signature = 'data-integrity:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Run Data Integrity checks';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		(new PaymentsCheck())->check();
		(new PresentablesOrderNumberCheck())->check();

		return $this->output->text("Checked!");
	}
}
