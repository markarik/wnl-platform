<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserTime;
use App\Models\User;
use Carbon\Carbon;

class CheckSatisfactionGuarantee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:satisfactionGuarantee {maxDate} {userId}';
	//maxDate should be formatted like: 2018-09-22

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
		$dateString = strval($this->argument('maxDate')).' 00:00:00';
		$user = User::find($userId);
		$profileId = $user->profile()->id;
		dd($profileId);

		$maxUserTimeId = $user->userTime()
			->whereDate('created_at','<=', $dateString)
			->orderBy('id', 'desc')
			->first();

		print 'User time in minutes '.$maxUserTimeId->time."\n";
		print 'User time in hours '.($maxUserTimeId->time/60)."\n";


    }
}
