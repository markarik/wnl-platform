<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class QuizTestDumpRedis extends Command
{
	protected $redis;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quizTest:dumpRedis {mode}';

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
		$this->redis = Redis::connection();
		$this->info('[Redis] Retrieving keys');
		$keys = collect($this->redis->keys('UserState:Quiz:*'));
		$mode = $this->argument('mode');
		$shit = [];

		$total = count($keys);
		foreach ($keys as $i => $key) {
			list ($a, $b, $quizId, $userOrProfileId, $c) = explode(':', $key);

			if ($mode === 'before') {
				$user = User::whereHas('profile', function ($query)
				use ($userOrProfileId) {
					$query->where('id', $userOrProfileId);
				})->first();
			} else {
				$user = User::where('id', $userOrProfileId)->first();
			}

			$content = $this->redis->get($key);
			$score = json_decode($content, true)['attempts'][0]['score'];

			array_push($shit, [
				$user->id ?? 'null',
				$quizId,
				$score,
			]);

			if ($i % 500 === 0) print "$i/$total";
			if (!$user) {
				print 'E';
			} else {
				print '.';
			}
		}

		\Storage::put("quiz_test_dump_{$mode}.json", json_encode($shit));

		return;
	}
}
