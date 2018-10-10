<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserCourseProgress;
use App\Models\Lesson;
use App\Models\UserQuizResults;
use App\Models\QuizQuestion;
use Storage;

class ExportUserStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'userStatistics:export {maxDate} {products*}';
	// example date format: 2018-09-22

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export user statistics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

	const DEFAULT_GROUPS = [
		[
			'lessons_percentage' => ['min' => 80],
			'quiz_completation' => ['min' => 60],
			'time_spent' => ['min' => 300],
		],
		[
			'lessons_percentage' => ['min' => 30, 'max' => 80],
			'quiz_completation' => ['min' => 60],
			'time_spent' => ['min' => 100, 'max' => 300],
		],
		[
			'lessons_percentage' => ['max' => 30],
			'quiz_completation' => ['max' => 60],
			'time_spent' => ['max' => 100],
		],
	];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$productIds = $this->argument('products');
		$maxDate = \Carbon\Carbon::parse($this->argument('maxDate'));
		$headers = ['Id', 'Imię', 'Nazwisko', 'Czas spędzony na platformie',
		'Procent ukończonych lekcji', 'Procent rozwiązanych pytań'];

		foreach (self::DEFAULT_GROUPS as $key => $group) {
			$contents = $this->generateStats($group);
			$filename = $key+1;
			Storage::put("exports/stats/{$filename}.csv", $contents);
		}
	}

	protected function generateStats($groupCriteria) {
		// get users matching criteria
		$users = $this->getMatchingUsers($groupCriteria);

		// generate list
	}

	protected function getMatchingUsers ($groupCriteria) {
		$users = User::whereHas('orders', function($query) {
			$query->whereIn('product_id', $this->productIds)
				->where('paid', 1);
		});



		$userCourseProgress = UserCourseProgress::select()
			->where('user_id', $user->profile->id)
			->whereDate('created_at', '<=', $maxDate)
			->whereNull('section_id')
			->whereNull('screen_id')
			->where('status', 'complete')
			->count();

		$userQuizQuestionsSolved = UserQuizResults::where('user_id', $userId)
			->whereDate('created_at', '<=', $maxDate)
			->groupBy('quiz_question_id')
			->get(['quiz_question_id'])
			->count();
	}

	public function restOfIt () {
		$firstGroup = collect([$headers]);
		$secondGroup = collect([$headers]);
		$thirdGroup = collect([$headers]);

		$allLessons = Lesson::whereDate('created_at', '<=', $maxDate)->count();
		$allQuestions = QuizQuestion::whereDate('created_at', '<=', $maxDate)->count();

		$users = User::whereHas('orders', function($query) use ($productIds) {
			$query->whereIn('product_id', $productIds)
				->where('paid', 1);
		})->get();

		$bar = $this->output->createProgressBar($users->count());

		foreach ($users as $user) {
			if ($user->role) {
				continue;
			}

			$profileId = $user->profile->id;
			$userId = $user->id;

			$maxUserTime = $user->userTime()
				->whereDate('created_at','<=', $maxDate)
				->orderBy('id', 'desc')
				->first();

			if($maxUserTime) {
				$userTime = round($maxUserTime->time/60);
			} else {
				$userTime = 'brak';
			}

			$userCourseProgress = UserCourseProgress::where('user_id', $profileId)
				->whereDate('created_at', '<=', $maxDate)
				->whereNull('section_id')
				->whereNull('screen_id')
				->where('status', 'complete')
				->count();

			$userCourseProgressPrecentage = round(($userCourseProgress/$allLessons)*100);

			$userQuizQuestionsSolved = UserQuizResults::where('user_id', $userId)
				->whereDate('created_at', '<=', $maxDate)
				->groupBy('quiz_question_id')
				->get(['quiz_question_id'])
				->count();

			$userQuizQuestionsSolvedPercentage = round(($userQuizQuestionsSolved/$allQuestions)*100);

			if (
				$userCourseProgressPrecentage >= 80 &&
				$userQuizQuestionsSolvedPercentage >= 60 &&
				$userTime >= 100
			) {
				$firstGroup->push([
					$userId,
					$user->first_name,
					$user->last_name,
					$userTime,
					$userCourseProgressPrecentage,
					$userQuizQuestionsSolvedPercentage,
				]);
			} else if (
				$userCourseProgressPrecentage < 30 &&
				$userQuizQuestionsSolvedPercentage < 60 &&
				$userTime < 100
			) {
				$thirdGroup->push([
					$userId,
					$user->first_name,
					$user->last_name,
					$userTime,
					$userCourseProgressPrecentage,
					$userQuizQuestionsSolvedPercentage,
				]);
			} else {
				$secondGroup->push([
					$userId,
					$user->first_name,
					$user->last_name,
					$userTime,
					$userCourseProgressPrecentage,
					$userQuizQuestionsSolvedPercentage,
				]);
			}

			$bar->advance();
		}
		$bar->finish();

		dd($firstGroup."\n".$secondGroup."\n".$thirdGroup);

		// $firstGroup = $firstGroup->map(function ($row) {
		// 	return implode("\t", $row);
		// });
		//
		// $contents = $firstGroup->implode("\n");
		// Storage::put('exports/' . $name . '.tsv', $contents);
		//
		// return;
    }
}
