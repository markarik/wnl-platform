<?php

namespace App\Console\Commands;

use App\Models\User;
use Faker\Factory;
use Illuminate\Console\Command;

class AnonymizeUsers extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'users:anonymize {--includeAdmins}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Replaces users profiles data with fake data. DO NOT USE ON PRODUCTION GODDAMIT!';

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
		if (!$this->confirm('This command will overwrite ALL USERS NAMES. Do you really with to continue?')) return;

		$users = User::all();
		$bar = $this->output->createProgressBar(count($users));
		$faker = Factory::create();

		foreach ($users as $user) {
			$bar->advance();
			if ($user->hasRole('admin') && !$this->option('includeAdmins')) continue;

			$profile = $user->profile;
			$profile->first_name = $faker->firstName;
			$profile->last_name = $faker->lastName;
			$profile->public_email = $faker->email;
			$profile->public_phone = $faker->phoneNumber;
			$profile->username = $faker->firstName . $faker->lastName;
			$profile->avatar = null;
			$profile->save();
		}

		$bar->finish();

		echo "\n\n";
	}
}
