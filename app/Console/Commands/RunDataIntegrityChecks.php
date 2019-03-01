<?php

namespace App\Console\Commands;

use Checks\DataIntegrity\PaymentsCheck;
use Checks\DataIntegrity\PresentablesOrderNumberCheck;

class RunDataIntegrityChecks extends CommandWithMonitoring
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
	public function handleCommand() {
		(new PaymentsCheck())->check();
		(new PresentablesOrderNumberCheck())->check();

		$this->output->text("Checked!");
	}
}
