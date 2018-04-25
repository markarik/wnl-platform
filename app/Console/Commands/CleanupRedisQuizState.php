<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PrivateApi\UserQuizResultsApiController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CleanupRedisQuizState extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:cleanup-redis';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Remove redundant data in redis regarding quiz questions state';

	/**
	 * Create a new command instance.
	 *
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
		$keyPattern = UserQuizResultsApiController::getQuizRedisKey('*', '*');
		$allKeys = Redis::keys($keyPattern);
		$bar = $this->output->createProgressBar(count($allKeys));

		foreach ($allKeys as $key) {
			$dataRaw = Redis::get($key);
			if (!empty($dataRaw)) {
				$data = json_decode($dataRaw, true);
				$compressedData = [
					'setId' => $data['setId'],
					'setName' => $data['setName'],
					'attempts' => $data['attempts'],
					'isComplete' => $data['isComplete'],
					'questionsIds' => $data['questionsIds'],
					'quiz_questions' => array_map(function($question) {
						return [
							'isResolved' => $question['isResolved'] ?? false
						];
					}, $data['quiz_questions'])
				];
				Redis::set($key, json_encode($compressedData));
				$bar->advance();
			}
		}
		echo PHP_EOL;
	}
}
