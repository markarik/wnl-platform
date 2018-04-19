<?php

namespace App\Console\Commands;

use Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Controllers\Api\PrivateApi\UserStateApiController;
use Illuminate\Console\Command;

class UsersStatsExport extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	 protected $signature = 'users:stats
	 						{--R|roles= : Fill with a role ID if a user has to have it to be exported (or comma separated list)}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export a users list with statistics';

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
		if ($this->option('roles')) {
			$roles = explode(',', $this->option('roles'));
			$users = User::whereHas('roles', function ($query) use ($roles) {
				$query->whereIn('roles.id', $roles);
			})->get();
		} else {
			$users = User::all();
		}

		$data = [];
		$bar = $this->output->createProgressBar(count($users));

		foreach ($users as $user) {
			$stats = UserStateApiController::countUserStats($user);

			$line = [
				$user->id,
				$user->first_name,
				$user->last_name,
				$stats['time']['minutes'],
				$stats['lessons']['completed'],
				$stats['lessons']['started'],
				$stats['quiz_questions']['solved'],
			];

			$data[] = implode(',', $line);

			$bar->advance();
		}

		$bar->finish();
		$this->info("\n");

		Storage::put('exports/users_stats_' . time() . '.csv', implode(PHP_EOL, $data));

		return;
	}
}
