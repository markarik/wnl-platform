<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MigrateUserData extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:migrate-data';

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
			$user->profile()->firstOrCreate([
				'first_name' => $user->first_name,
				'last_name'  => $user->last_name,
			]);

			$user->userAddress()->firstOrCreate([
				'street' => $user->address,
				'zip'    => $user->zip,
				'city'   => $user->city,
				'phone'  => $user->phone,
			]);

			$user->billing()->firstOrCreate([
				'company_name' => $user->invoice_name,
				'vat_id'       => $user->invoice_nip,
				'address'      => $user->invoice_address,
				'zip'          => $user->invoice_zip,
				'city'         => $user->invoice_city,
				'country'      => $user->invoice_country,
			]);
		}
	}
}
