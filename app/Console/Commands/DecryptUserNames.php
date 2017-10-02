<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DecryptUserNames extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:decryptNames';

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
		foreach (User::all() as $user) {
			$user->first_name = decrypt($user->first_name);
			$user->last_name = decrypt($user->last_name);
			$user->save();
		}

		$this->info('Done.');

		return;
	}
}
