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

		$this->seedQuiz('Example test set', 'app/quiz_hits.yaml');
		$this->seedQuiz('Pytania z reumatologii', 'app/quiz_reumatologia_hits.yaml');
	}

	protected function seedQuiz($name, $file)
	{
		$set = QuizSet::create([
			'name' => $name,
		]);

		$quiz = yaml_parse_file(storage_path($file));
//		foreach ($quiz['questions'] as $index => $question) {
//			foreach ($question['answers'] as $answerIndex => $answer) {
//				if (array_key_exists('is_correct', $answer)) {
//					$quiz['questions'][$index]['answers'][$answerIndex]['hits'] = rand(0, 200);
//				} else {
//					$quiz['questions'][$index]['answers'][$answerIndex]['hits'] = rand(0, 50);
//				}
//			}
//		}
//		yaml_emit_file(storage_path('app/quiz_hits.yaml'), $quiz);

		foreach ($quiz['questions'] as $question) {
			$preserveOrder = 0;
			if (array_key_exists('preserve_order', $question)) {
				$preserveOrder = $question['preserve_order'];
			}
			$newQuestion = $set->questions()->create([
				'text'           => $question['text'],
				'preserve_order' => $preserveOrder,
			]);
			foreach ($question['answers'] as $answer) {
				$isCorrect = 0;
				if (array_key_exists('is_correct', $answer)) {
					$isCorrect = $answer['is_correct'];
				}
				$newQuestion->answers()->create([
					'text'       => $answer['text'],
					'hits'       => $answer['hits'],
					'is_correct' => $isCorrect,
				]);
			}
		}
	}

}
