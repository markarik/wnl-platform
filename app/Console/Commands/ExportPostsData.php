<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\QnaAnswer;
use App\Models\QnaQuestion;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExportPostsData extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'posts:export';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Exports data about posts added by users';

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
		$comments = Comment::all();
		$qnaQuestions = QnaQuestion::all();
		$qnaAnswers = QnaAnswer::all();

		$startDate = Carbon::parse('2017-06-17');
		$now = new Carbon();

		$this->info("Counting from {$startDate->toDateString()} \n");
		$exportData = [];

		for ($date = $startDate->copy(); $date->lte($now); $date->addDay()) {
			$sum = $comments->where('created_at', '<=', $date)->count() +
				$qnaQuestions->where('created_at', '<=', $date)->count() +
				$qnaAnswers->where('created_at', '<=', $date)->count();

			$this->info($date->toDateString() . ' - ' . $sum . "\n");
			$exportData[] = $date->toDateString() . ',' . $sum;
		}

		$csv = implode(PHP_EOL, $exportData);

		\Storage::put('exports/posts- ' . $now->toDateTimeString() . '.csv', $csv);
	}
}
