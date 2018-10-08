<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ExportIdentityNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'identityNumbers:export {products*}';

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
		$users = User::whereHas('orders', function($query) use ($productIds) {
			$query->whereIn('product_id', $productIds)
				->where('paid', 1);
		})->whereHas('personalData')->get();

		foreach ($users as $user) {
			// dd($user->personalData()->get());
			// dd($user->personalData()->get([
			// 	'personal_identity_number',
			// 	'identity_card_number',
			// 	'passport_number'
			// ])->toArray());
			print $user->personalData()->get([
				'personal_identity_number',
				'identity_card_number',
				'passport_number'
			])->values();
			print PHP_EOL;
		}
		print 'Total number of records '.$users->count();
    }
}
