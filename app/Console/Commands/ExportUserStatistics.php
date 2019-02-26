<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Role;
use App\Models\UserQuizResults;
use Illuminate\Console\Command;
use App\Models\User;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;
use Illuminate\Support\Collection;

class ExportUserStatistics extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature =
		"userStatistics:export {products* : The list of products user has to have access to.}";

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
	 * @throws \Box\Spout\Common\Exception\IOException
	 * @throws \Box\Spout\Common\Exception\InvalidArgumentException
	 * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
	 * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
	 */
	public function handle()
	{
		$start = microtime(true);
		$dbName = env('DB_DATABASE');
		$newestRecord = UserQuizResults::orderBy('id', 'desc')->limit(1)->first()->created_at;

		$productIds = $this->argument('products');
		$products = Product::whereIn('id', $productIds)->get();

		$minDate = $products->first()->signups_start;
		$maxDate = $products->first()->course_end;


		$this->info("Exporting user stats for date range from {$minDate} to {$maxDate}.");
		$this->info("Database: {$dbName}, newest record from {$newestRecord}");

		$groups = $this->getUserGroups($minDate, $maxDate, $productIds);

		$total = 0;
		$groups = $groups
			->filter(function ($group) {
				return $group->count();
			})
			->map(function ($group, $key) use (&$total) {
				$recordsCount = $group->count();
				$total += $recordsCount;
				$this->info("Group {$key} has {$recordsCount} records.");

				return $group->map(function ($userRecord) {
					$user = $userRecord['user'];
					return [
						'Id'                             => $user->id,
						'Imię'                           => $user->first_name,
						'Nazwisko'                       => $user->last_name,
						'Czas spędzony na platformie'    => $userRecord['time'],
						'Procent przerobionych sekcji'   => $userRecord['userSectionsProgressPercentage'],
						'Rozwiązanych pytań zamkniętych' => $userRecord['userQuizQuestionsSolved'],
						'Rozwiązanych pytań otwartych'   => $userRecord['userFlashcardsSolved'],
						'Produkty'                       => $userRecord['products']->implode(' | '),
					];
				});
			});

		$filename = storage_path('app/exports/user_stats.xlsx');
		$sheets = new SheetCollection($groups);
		(new FastExcel($sheets))->export($filename);

		$this->info("Saved under {$filename}");
		$this->info("Total records: {$total}");

		$time = number_format(microtime(true) - $start, 2);
		$this->info("Finished in {$time} seconds");

		return;
	}

	protected function getUserGroups($minDate, $maxDate, $productIds)
	{
		$oldProducts = [1, 2, 5, 6, 9, 10];
		$groups = collect([
			'N-G1' => collect(),
			'N-G2' => collect(),
			'N-G3' => collect(),
			'N-G4' => collect(),
			'N-G5' => collect(),

			'P-G1' => collect(),
			'P-G2' => collect(),
			'P-G3' => collect(),
			'P-G4' => collect(),
			'P-G5' => collect(),

			'N-KORELACJE' => collect(),
			'P-KORELACJE' => collect(),
		]);

		/** @var User[]|Collection $users */
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
			$userRecord['user'] = $user;
			$courseProgressStats = $user->getCourseProgressStats($minDate, $maxDate);
			$userRecord['userQuizQuestionsSolved'] = $courseProgressStats['quiz_questions_solved'];
			$userRecord['userFlashcardsSolved'] = $courseProgressStats['flashcards_solved'];
			$userRecord['userSectionsProgressPercentage'] = $courseProgressStats['sections_progress_perc'];
			$userRecord['time'] = $courseProgressStats['time'];
			$userRecord['products'] = $user->orders()
				->where('paid', 1)
				->pluck('product_id')
				->unique();

			$newUser = !$userRecord['products']->contains(function ($productId) use ($oldProducts) {
				return in_array($productId, $oldProducts);
			});

			$G1 =
				$userRecord['userSectionsProgressPercentage'] >= 80 &&
				$userRecord['userQuizQuestionsSolved'] >= 1700 &&
				$userRecord['time'] >= 240;

			$G2 =
				$userRecord['userSectionsProgressPercentage'] >= 80 &&
				$userRecord['time'] >= 240;

			$G3 =
				$userRecord['userQuizQuestionsSolved'] >= 1700 &&
				$userRecord['time'] >= 240;

			$G4 =
				$userRecord['userSectionsProgressPercentage'] >= 20 ||
				$userRecord['userQuizQuestionsSolved'] >= 300 ||
				$userRecord['time'] >= 100;

			$G5 = !$G1 && !$G2 && !$G3 && !$G4;

			$userClassification = [
				'N-G1' => $newUser && $G1,
				'N-G2' => $newUser && ($G1 || $G2),
				'N-G3' => $newUser && ($G1 || $G3),
				'N-G4' => $newUser && !$G1 && !$G2 && !$G3 && $G4,
				'N-G5' => $newUser && $G5,

				'P-G1' => !$newUser && $G1,
				'P-G2' => !$newUser && ($G1 || $G2),
				'P-G3' => !$newUser && ($G1 || $G3),
				'P-G4' => !$newUser && !$G1 && !$G2 && !$G3 && $G4,
				'P-G5' => !$newUser && $G5,

				'N-KORELACJE' => $newUser && !$G5,
				'P-KORELACJE' => !$newUser && !$G5,
			];

			foreach ($groups as $groupName => $group) {
				if ($userClassification[$groupName]) {
					$groups[$groupName]->push($userRecord);
				}
			}

			$bar->advance();
		}
		$bar->finish();

		print PHP_EOL;

		return $groups;
	}
}
