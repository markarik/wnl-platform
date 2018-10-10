<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserTime;
use App\Models\User;
use App\Models\Lesson;
use App\Models\QuizQuestion;
use App\Models\UserCourseProgress;
use App\Models\UserQuizResults;

class CheckSatisfactionGuarantee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:satisfactionGuarantee {maxDate} {userId} {productIds*}';
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
		$profileId = $user->profile->id;
		$productIds = $this->argument('productIds');

		$maxUserTime = $user->userTime()
			->whereDate('created_at','<=', $dateString)
			->orderBy('id', 'desc')
			->first();

		if ($maxUserTime) {
			$actualTime = $maxUserTime->time;
		} else {
			$actualTime = 'No user time';
		}

		print PHP_EOL;
		print $user->getFullNameAttribute()."\n";
		print 'Profile id '.$profileId."\n";
		print 'Id '.$userId."\n";
		print 'User time in minutes '.$actualTime."\n";
		print 'User time in hours '.round($actualTime/60)."\n";

		$allLessons = Lesson::whereDate('created_at', '<=', $dateString)->count();
		$allQuestions = QuizQuestion::whereDate('created_at', '<=', $dateString)->count();
 		$users = User::whereHas('orders', function($query) use ($productIds) {
			$query->whereIn('product_id', $productIds)
				->where('paid', 1);
		})->get();

		$userCourseProgress = UserCourseProgress::where('user_id', $profileId)
			->whereDate('created_at', '<=', $dateString)
			->whereNull('section_id')
			->whereNull('screen_id')
			->where('status', 'complete')
			->count();

		print 'User course progress percentage '.round(($userCourseProgress/$allLessons)*100)."\n";

		$userQuizQuestionsSolved = UserQuizResults::where('user_id', $userId)
			->whereDate('created_at', '<=', $dateString)
			->groupBy('quiz_question_id')
			->get(['quiz_question_id'])
			->count();

		print 'User quiz questions solved percentage '.round(($userQuizQuestionsSolved/$allQuestions)*100)."\n";
		print PHP_EOL;

 		return;
    }
}
