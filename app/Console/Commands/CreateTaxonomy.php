<?php

namespace App\Console\Commands;

use App\Models\Taxonomy;
use Illuminate\Console\Command;

class CreateTaxonomy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:taxonomy {names*}';

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
        $names = $this->argument('names');

		foreach ($names as $name) {
			Taxonomy::firstOrCreate(['name' => $name]);
		}

		$this->info('OK.');
    }
}
