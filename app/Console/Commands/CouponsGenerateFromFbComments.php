<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Storage;

class CouponsGenerateFromFbComments extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'coupons:generateFb';

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
		$contents = Storage::drive()->get('import/facebook.json');
		$decoded = json_decode($contents, true);
		$comments = $decoded['comments']['data'];

		foreach ($comments as $comment) {
			$expires = Carbon::now()->addYears(20);
			Coupon::create([
				'code'         => mb_convert_case($comment['from']['name'], MB_CASE_UPPER, "UTF-8"),
				'name'         => 'Zniżka -10% dla uczestników konkursu',
				'type'         => 'percentage',
				'value'        => 10,
				'expires_at'   => $expires,
				'times_usable' => 1,
			]);
		}

		return 42;
	}
}
