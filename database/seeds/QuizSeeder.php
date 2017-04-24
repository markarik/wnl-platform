<?php

use Illuminate\Database\Seeder;

use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizSet;

class QuizSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		QuizQuestion::flushEventListeners();
		QuizAnswer::flushEventListeners();
		QuizSet::flushEventListeners();

		$set = QuizSet::create([
			'name' => 'Example test set',
		]);

		$quiz = yaml_parse_file(storage_path('app/quiz.yaml'));

		foreach ($quiz['questions'] as $question) {
			$newQuestion = $set->questions()->create([
				'text' => $question['text'],
			]);

			foreach ($question['answers'] as $answer) {
				$isCorrect = 0;
				$preserveOrder = 0;
				if (array_key_exists('is_correct', $answer)) {
					$isCorrect = $answer['is_correct'];
				}
				if (array_key_exists('preserve_order', $answer)) {
					$preserveOrder = $answer['preserve_order'];
				}
				$newQuestion->answers()->create([
					'text'           => $answer['text'],
					'is_correct'     => $isCorrect,
					'preserve_order' => $preserveOrder,
				]);
			}
		}
	}
}
