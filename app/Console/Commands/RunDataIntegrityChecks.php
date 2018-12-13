<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QnaQuestion;
use App\Models\Tag;
use Tests\DataIntegrity\PaymentsCheck;

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
		(new PaymentsCheck())->test();
	}
}
