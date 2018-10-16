<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

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
	protected $description = 'Command description';

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

		$productIds = $this->argument('products');
		$products = Product::whereIn('id', $productIds)->get();

		$minDate = $products->first()->signups_start;
		$maxDate = $products->first()->course_end;

		$this->info("Exporting user stats for date range from {$minDate} to {$maxDate}.");

		$headers = implode("\t", ['Imię', 'Nazwisko', 'PESEL', 'Numer dowodu osobisgtego']);

		$groups = $this->getUserGroups($minDate, $maxDate, $productIds);

		$total = 0;
		foreach ($groups as $key => $group) {
			$group = $group
				->filter(function ($user) {
					if(is_null($user->personalData)) return false;
					return
						$user->personalData->personal_identity_number ||
						$user->personalData->identity_card_number;
				});
			$recordsCount = $group->count();
			$total += $recordsCount;
			$this->info("Group {$key} has {$recordsCount} records.");
			$group = $group
				->map(function ($user) {
					return implode("\t", [
						$user->first_name,
						$user->last_name,
						$user->personalData->personal_identity_number ?? '',
						$user->personalData->identity_card_number ?? '',
					]);
				});
			$group->prepend($headers);
			$contents = $group->implode("\n");
			$path = 'exports/user-stats/' . $key . '.tsv';
			\Storage::put($path, $contents);
			$this->info("Saved under {$path}");
		}

		$this->info("Total records: {$total}");

		return;
	}
}
