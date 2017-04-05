<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class EncryptPasswords extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'pass:encrypt';

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
		$users = User::all();

		foreach ($users as $user) {
			$user->password = bcrypt($user->password);
			$user->save();
		}

		$this->comment(PHP_EOL . "OK" . PHP_EOL);
	}
}
