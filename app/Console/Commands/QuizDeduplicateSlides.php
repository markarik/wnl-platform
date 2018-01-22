<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QuizDeduplicateSlides extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:deduplicateSlides';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Deduplicate slides attached to quiz questions.';

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
	 * @return mixed
	 * @throws \Exception
	 */
	public function handle()
	{
		$allBindingsCount = \DB::table('slide_quiz_question')->count();
		\DB::beginTransaction();
		try {
			$uniqueBindings = \DB::table('slide_quiz_question')
				->select(['slide_id', 'quiz_question_id'])
				->groupBy(['slide_id', 'quiz_question_id'])
				->get();

			$aUniqueBindings = $uniqueBindings->map(function ($el) {
				return [
					'slide_id'         => $el->slide_id,
					'quiz_question_id' => $el->quiz_question_id,
				];
			})->toArray();
			\DB::table('slide_quiz_question')->delete();
			\DB::table('slide_quiz_question')->insert($aUniqueBindings);
		}
		catch (\Exception $exception) {
			\DB::rollBack();
			throw $exception;
		}
		\DB::commit();

		$removedCount = $allBindingsCount - $uniqueBindings->count();
		$this->info('OK. Removed bindings: ' . $removedCount);
		\Artisan::call('cache:tag', ['tag' => 'quiz_questions']);

		return;
	}
}
