<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Storage;

class ExportIdentityNumbersForCEM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'identityNumbers:export {maxDate} {products*}';
	// example date format: 2018-09-21

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export identity numbers for products';

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
		$dateString = strval($this->argument('maxDate')).' 00:00:00';

		$headers = ['Imię', 'Nazwisko', 'PESEL'];
		$rows = collect([$headers]);
		$name = 'Zaszyfrowane numery PESEL';

		$users = User::whereHas('orders', function($query) use ($productIds) {
			$query->whereIn('product_id', $productIds)
				->where('paid', 1);
		})->whereHas('personalData')->get();

		foreach ($users as $user) {
			
			if($user->roles) {
				return;
			}

			print $user->personalData()->get([
				'personal_identity_number',
				'identity_card_number',
				'passport_number'
			])->values()."\n";

			// $rows->push([
			// 	$user->first_name,
			// 	$user->last_name,
			//
			// ]);
		}
		// $rows = $rows->map(function ($row) {
		// 	return implode("\t", $row);
		// });
		//
		// $contents = $rows->implode("\n");
		// Storage::put('exports/' . $name . '.tsv', $contents);

		return;
    }
}
