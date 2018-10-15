<?php

namespace App\Console\Commands;

use App\Models\Product;
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
	protected $signature = 'userStatistics:export {products*}';
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
		$products = Product::whereIn('id', $productIds)->get();

		$minDate = $products->first()->signups_start;
		$maxDate = $products->first()->course_end;

		$this->info("Exporting user stats for date range from {$minDate} to {$maxDate}.");

		$headers = ['Id', 'Imię', 'Nazwisko', 'Czas spędzony na platformie', 'Procent ukończonych lekcji',
			'Procent rozwiązanych pytań'];

		$firstGroup = collect([$headers]);
		$secondGroup = collect([$headers]);
		$thirdGroup = collect([$headers]);

		$allLessons = Lesson::select(['id'])
			->whereDate('created_at', '<=', $maxDate)
			->where('is_required', 1)
			->count();
		$allQuestions = QuizQuestion::whereDate('created_at', '<=', $maxDate)->count();

		$users = User::whereHas('orders', function ($query) use ($productIds) {
			$query
				->whereIn('product_id', $productIds)
				->where('paid', 1);
		})->get();

		$bar = $this->output->createProgressBar($users->count());

		foreach ($users as $user) {
			if ($user->role) {
				continue;
			}

			$profileId = $user->profile->id;
			$userId = $user->id;

			$timeCollection = $user->userTime()
				->whereBetween('created_at', [$minDate, $maxDate])
				->orderBy('id', 'desc')
				->get();

			$userTime = (int)round(($timeCollection->max('time') - $timeCollection->min('time')) / 60);

			$userCourseProgress = UserCourseProgress::where('user_id', $profileId)
				->whereDate('user_course_progress.created_at', '<=', $maxDate)
				->join('lessons', 'lessons.id', '=', 'lesson_id')
				->where('lessons.is_required', 1)
				->whereNull('user_course_progress.section_id')
				->whereNull('user_course_progress.screen_id')
				->where('user_course_progress.status', 'complete')
				->count();

			$userCourseProgressPrecentage = (int)round(($userCourseProgress / $allLessons) * 100);

			$userQuizQuestionsSolved = UserQuizResults::where('user_id', $userId)
				->groupBy('quiz_question_id')
				->get(['quiz_question_id'])
				->count();

			$userQuizQuestionsSolvedPercentage = (int)round(($userQuizQuestionsSolved / $allQuestions) * 100);

			$userRecord = [
				$userId,
				$user->first_name,
				$user->last_name,
				$userTime,
				$userCourseProgressPrecentage,
				$userQuizQuestionsSolvedPercentage,
			];

			$firstGroupCriteria =
				$userCourseProgressPrecentage >= 80 &&
				$userQuizQuestionsSolvedPercentage >= 60 &&
				$userTime >= 300;

			$secondGroupCriteria =
				$userCourseProgressPrecentage >= 30 &&
				$userQuizQuestionsSolvedPercentage >= 30 &&
				$userTime >= 100;

			if ($firstGroupCriteria) {
				$firstGroup->push($userRecord);
			} else if ($secondGroupCriteria) {
				$secondGroup->push($userRecord);
			} else {
				$thirdGroup->push($userRecord);
			}

			$bar->advance();
		}
		$bar->finish();

		print PHP_EOL;

		$groups = [
			$firstGroup,
			$secondGroup,
			$thirdGroup
		];

		$total = 0;

		foreach ($groups as $key => $group) {
			$recordsCount = $group->count();
			$total += $recordsCount;
			$this->info("Group {$key} has {$recordsCount} records.");
			$group = $group->map(function ($row) {
				return implode("\t", $row);
			});
			$contents = $group->implode("\n");
			$path = 'exports/user-stats/' . $key . '.tsv';
			Storage::put($path, $contents);
			$this->info("Saved under {$path}");
		}

		$this->info("Total records: {$total}");

		return;
	}
}
