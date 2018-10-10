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

		$users = User::whereHas('orders', function($query) use ($productIds) {
			$query->whereIn('product_id', $productIds)
				->where('paid', 1);
		})->whereHas('personalData')->get();

		foreach ($users as $user) {
			print $user->first_name.' ';
			print $user->last_name.' ';
			print $user->personalData()->get([
				'personal_identity_number',
				'identity_card_number',
				'passport_number'
			])->values();
			// użytkownik 1069, który nie ma wpisu w user_time ale ma user_personal_data -> czo wtedy? komenda się wyjebuje.
			// czy to w ogóle możliwe mieć wpis w user_personal_data a nie mieć w user_time?
			// czy trzeba if'ować każdy warunek?
			print PHP_EOL;
		}
		print 'Total number of records '.$users->count();
    }
}
