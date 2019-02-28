<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;

class RoleAdd extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'role:add {name}';

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
		$this->info('OK, role ID is ' .
			Role::create([
				'name' => $this->argument('name'),
			])->id
		);
	}
}
