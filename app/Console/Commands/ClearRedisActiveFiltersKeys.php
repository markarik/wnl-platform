<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ClearRedisActiveFiltersKeys extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'clear-redis:active-filters';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "PLAT-984 - Remove all cached filters in questions bank for each user.";

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$keys = Redis::keys("active-filters-user-*-resource-quiz_questions");

		foreach ($keys as $key) {
			Redis::del($key);
		}
	}
}
