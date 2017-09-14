<?php

namespace App\Console\Commands;

use App\Models\User;
use Faker\Factory;
use Illuminate\Console\Command;
use Storage;

class AnonymizeUsers extends Command
{
	const VALUE_DELIMITER = ",";
	const ROW_DELIMITER = "\n";

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'users:anonymize {file?} {--includeAdmins}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Replaces users profiles data with fake data. DO NOT USE ON PRODUCTION GODDAMIT!';

	protected $dataset = null;

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

		$filename = $this->argument('file');

		if ($filename) {
			$file = Storage::get($filename);

			if ($file) {
				$this->dataset = explode(self::ROW_DELIMITER, $file);
			}
		}

		$users = User::all();
		$bar = $this->output->createProgressBar(count($users));

		$faker = Factory::create();
		$i = 1;

		foreach ($users as $user) {
			$bar->advance();
			if ($user->hasRole('admin') && !$this->option('includeAdmins')) continue;

			$data = $this->getFakeData($faker, $i);

			$profile = $user->profile;
			$profile->first_name = $data['first_name'];
			$profile->last_name = $data['last_name'];
			$profile->public_email = $data['public_email'];
			$profile->public_phone = $data['public_phone'];
			$profile->username = $data['username'];
			$profile->avatar = null;
			$profile->save();

			$i++;
		}

		$bar->finish();

		echo "\n\n";
	}

	protected function getFakeData($faker, $row = 0) {
		if ($this->dataset) {
			$dataset = explode(self::VALUE_DELIMITER, $this->dataset[$row]);
			return [
				'first_name' => $dataset[0],
				'last_name' => $dataset[1],
				'public_email' => $dataset[2],
				'username' => $dataset[0].$dataset[1].$row,
				'public_phone' => $dataset[4],
			];
		}

		return [
			'first_name' => $faker->firstName,
			'last_name' => $faker->lastName,
			'public_email' => $faker->email,
			'username' => $faker->firstName . $faker->lastName,
			'public_phone' => $faker->phoneNumber,
		];
	}
}
