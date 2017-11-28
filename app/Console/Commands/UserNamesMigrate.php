<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Console\Command;

class UserNamesMigrate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'userNames:migrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'userNames migration';

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
        $this->migrateDisplayName();
        $this->migrateRealName();
    }

    public function migrateRealName()
    {
        $this->info('migrating real names...');
        $users = User::with(['profile'])->get();
        $bar = $this->output->createProgressBar(count($users));
		foreach ($users as $user)  {
            $user->profile->first_name = $user->first_name;
            $user->profile->last_name = $user->last_name;
            $user->profile->save();
            $bar->advance();
		}
        $bar->finish();
        print PHP_EOL;
    }

    public function migrateDisplayName()
    {
        $this->info('migrating display names...');
        $userProfiles = UserProfile::all();
        $bar = $this->output->createProgressBar(count($userProfiles));
		foreach ($userProfiles as $userProfile)  {
            $userProfile->display_name = $userProfile->full_name;
            $userProfile->save();
            $bar->advance();
		}
        $bar->finish();
        print PHP_EOL;
	}
}
