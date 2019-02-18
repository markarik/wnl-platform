<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QnaQuestion;
use App\Models\Tag;

class AddContext extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'context:add';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add context to QNA questions';

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
		/** @var QnaQuestion[] $qnaQuestions */
		$qnaQuestions = QnaQuestion::all();

		foreach ($qnaQuestions as $question) {
			if (!empty($question->screen)) {
				$screenId = $question->screen->id;
				$lessonId = $question->screen->lesson->id;

				if (empty($question->meta)) {
					$question->meta = ['context' => [
						'name' => 'screens',
						'params' => [
							'lessonId' => $lessonId,
							'screenId' => $screenId,
							'courseId' => 1
						]
					]];

					$question->save();
				}
			// Question is from "Pomoc w nauce" route
			} else if ($question->tags->contains(Tag::find(1))) {
				$question->meta = ['context' => [
					'name' => 'help-learning',
					'params' => []
				]];

				$question->save();
			// Question is from "Pomoc techniczna" route
			} else if ($question->tags->contains(Tag::find(2)) && $question->tags->contains(Tag::find(3))) {
				$question->meta = ['context' => [
					'name' => 'help-tech',
					'params' => []
				]];

				$question->save();
			} else if ($question->tags->contains(Tag::find(4)) && $question->tags->contains(Tag::find(5))) {
				$question->meta = ['context' => [
					'name' => 'help-new',
					'params' => []
				]];

				$question->save();
			}
		}
	}
}
