<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Concerns\ProvidesApiFiltering;
use App\Models\Discussion;
use App\Models\Page;
use App\Models\QnaQuestion;
use App\Models\Screen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class MigrateRedisKeys extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'data-migration:redis-active-filters';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "PLAT-984 - Migrate redis active filters.";

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$keys = Redis::keys("active-filters-user-*-resource-quiz_questions");

		foreach ($keys as $key) {
			$value = Redis::get($key);

			list($filters, $active) = json_decode($value, true);

			$cleanedUpFilters = [];

			foreach ($filters as $filter) {
				$result = array_filter($filter, function($f) {
					$list = array_filter($f['list'], function($v) {
						return !empty($v);
					});

					return !empty($list);
				});

				if (!empty($result)) $cleanedUpFilters[] = $result;
			}

			$cleanedUpActive = [];
			foreach ($active as $ac) {
				$match = preg_match("/\[\d+\]/i", $ac);
				if (empty($match)) $cleanedUpActive[] = $ac;
			}

			$data = json_encode([$cleanedUpFilters, $cleanedUpActive]);
			Redis::set($key, $data);
		}
	}
}
