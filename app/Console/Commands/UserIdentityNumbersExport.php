<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\UserQuizResults;
use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

class UserIdentityNumbersExport extends ExportUserStatistics
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'users:exportIdentityNumbers {products*}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export user identity numbers for given products.';

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
		if (!\App::environment('dev')) {
			$this->warn("We don't want to store identity numbers unencrypted on our servers.");
			$this->warn("This command is meant to be used in dev environment only.");
			die;
		}

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
				$group = $group
					->filter(function ($userRecord) {
						$user = $userRecord['user'];
						return !is_null($user->personalData) && !empty($user->personalData->personal_identity_number);
					});

				$recordsCount = $group->count();
				$total += $recordsCount;
				$this->info("Group {$key} has {$recordsCount} records.");

				return $group->map(function ($userRecord) {
						$user = $userRecord['user'];
						return [
							'Id'                             => $user->id,
							'PESEL'                          => $user->personalData->personal_identity_number ?? '',
							'Czas spędzony na platformie'    => $userRecord['time'],
							'Procent przerobionych sekcji'   => $userRecord['userSectionsProgressPercentage'],
							'Rozwiązanych pytań zamkniętych' => $userRecord['userQuizQuestionsSolved'],
							'Rozwiązanych pytań otwartych'   => $userRecord['userFlashcardsSolved'],
							'Produkty'                       => $userRecord['products']->implode(' | '),
						];
					});
			});

		$filename = storage_path('app/exports/identity_numbers.xlsx');
		$sheets = new SheetCollection($groups);
		(new FastExcel($sheets))->export($filename);

		$this->info("Saved under {$filename}");
		$this->info("Total records: {$total}");

		$time = number_format(microtime(true) - $start, 2);
		$this->info("Finished in {$time} seconds");

		return;
	}
}
