<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserTime;

class CheckSatisfactionGuarantee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:satisfactionGuarantee {maxDate} {userId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for satisfaction guarantee conditions';

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
		$userId = $this->argument('userId');
		$date = $this->argument('maxDate');
		dd($date. $userId);
    }
}
