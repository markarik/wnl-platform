<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Role;
use App\Models\UserQuizResults;
use Illuminate\Console\Command;
use App\Models\User;
use Storage;

class ExportUserStatistics extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature =
		"userStatistics:export {products* : The list of products user has to have access to. The dates are taken for the first product from the list}";

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
		dump(env('DB_DATABASE'));
		dump(UserQuizResults::max('created_at'));
		$productIds = $this->argument('products');
		$products = Product::whereIn('id', $productIds)->get();

		$minDate = $products->first()->signups_start;
		$maxDate = $products->first()->course_end;

		$this->info("Exporting user stats for date range from {$minDate} to {$maxDate}.");

		$headers = implode("\t", [
			'Id',
			'Imię',
			'Nazwisko',
			'Czas spędzony na platformie',
			'Procent ukończonych lekcji',
			'Procent przerobionych sekcji',
			'Procent rozwiązanych pytań',
			'Produkty',
		]);

		$groups = $this->getUserGroups($minDate, $maxDate, $productIds);

		$total = 0;
		foreach ($groups as $key => $group) {
			$recordsCount = $group->count();
			$total += $recordsCount;
			$this->info("Group {$key} has {$recordsCount} records.");
			$group = $group->map(function ($user) {
				return implode("\t", [
					$user->id,
					$user->first_name,
					$user->last_name,
					$user->time,
					$user->userCourseProgressPercentage,
					$user->userSectionsProgressPercentage,
					$user->userQuizQuestionsSolvedPercentage,
					$user->orders()->where('paid', 1)->pluck('product_id')->unique()->implode(' | '),
				]);
			});
			$group->prepend($headers);
			$contents = $group->implode("\n");
			$path = 'exports/user-stats/' . $key . '.tsv';
			Storage::put($path, $contents);
			$this->info("Saved under {$path}");
		}

		$this->info("Total records: {$total}");

		return;
	}

	protected function getUserGroups($minDate, $maxDate, $productIds)
	{
		$firstGroup = collect();
		$secondGroup = collect();
		$thirdGroup = collect();

		$users = User::select()
			->whereHas('orders', function ($query) use ($productIds) {
				$query
					->whereIn('product_id', $productIds)
					->where('paid', 1);
			})
			->whereDoesntHave('roles', function ($query) {
				$query->whereIn('name', [Role::ROLE_ADMIN, Role::ROLE_MODERATOR, Role::ROLE_TEST]);
			})
			->get();

		$bar = $this->output->createProgressBar($users->count());

		foreach ($users as $user) {
			$courseProgressStats = $user->getCourseProgressStats($minDate, $maxDate);
			$user->userCourseProgressPercentage = $courseProgressStats['course_progress_perc'];
			$user->userQuizQuestionsSolvedPercentage = $courseProgressStats['quiz_questions_solved_perc'];
			$user->userSectionsProgressPercentage = $courseProgressStats['sections_progress_perc'];
			$user->time = $courseProgressStats['time'];

			$firstGroupCriteria = $user->hasFinishedCourse($minDate, $maxDate);

			$secondGroupCriteria =
				$user->userCourseProgressPercentage >= 30 &&
				$user->userQuizQuestionsSolvedPercentage >= 30 &&
				$user->time >= 100;

			if ($firstGroupCriteria) {
				$firstGroup->push($user);
			} else if ($secondGroupCriteria) {
				$secondGroup->push($user);
			} else {
				$thirdGroup->push($user);
			}

			$bar->advance();
		}
		$bar->finish();

		print PHP_EOL;

		return [$firstGroup, $secondGroup, $thirdGroup];
	}
}
