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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$productIds = $this->argument('products');
		$maxDate = $this->argument('maxDate');
		$dateString = strval($maxDate).' 00:00:00';

		$name = 'Statystyki użytkowników dla daty: '.$maxDate.' i produktów: '.$productIds[0].' '.$productIds[1];

		$headers = ['Id', 'Imię', 'Nazwisko', 'Czas spędzony na platformie', 'Procent ukończonych lekcji', 'Procent rozwiązanych pytań'];

		$firstGroup = collect([$headers]);
		$secondGroup = collect([$headers]);
		$thirdGroup = collect([$headers]);

		$allLessons = Lesson::whereDate('created_at', '<=', $dateString)->count();
		$allQuestions = QuizQuestion::whereDate('created_at', '<=', $dateString)->count();

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
				->whereDate('created_at','<=', $dateString)
				->orderBy('id', 'desc')
				->first();

			if($maxUserTime) {
				$userTime = round($maxUserTime->time/60);
			} else {
				$userTime = 'brak';
			}

			$userCourseProgress = UserCourseProgress::where('user_id', $profileId)
				->whereDate('created_at', '<=', $dateString)
				->whereNull('section_id')
				->whereNull('screen_id')
				->where('status', 'complete')
				->count();

			$userCourseProgressPrecentage = round(($userCourseProgress/$allLessons)*100);

			$userQuizQuestionsSolved = UserQuizResults::where('user_id', $userId)
				->whereDate('created_at', '<=', $dateString)
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
				($userCourseProgressPrecentage > 30 && $userCourseProgressPrecentage < 80) &&
				$userQuizQuestionsSolvedPercentage < 60 &&
				($userTime >= 100 && $userTime < 300)
			) {
				$secondGroup->push([
					$userId,
					$user->first_name,
					$user->last_name,
					$userTime,
					$userCourseProgressPrecentage,
					$userQuizQuestionsSolvedPercentage,
				]);
			} else {
				$thirdGroup->push([
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

		$groups = [
			$firstGroup,
			$secondGroup,
			$thirdGroup
		];

		foreach($groups as $key => $group) {
			$group = $group->map(function ($row) {
				return implode("\t", $row);
			});
			$contents = $group->implode("\n");
			Storage::put('exports/' . $key . '.tsv', $contents);
		}
		return;
    }
}
