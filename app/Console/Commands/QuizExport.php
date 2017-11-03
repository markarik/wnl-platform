<?php

namespace App\Console\Commands;

use App\Models\QuizSet;
use Illuminate\Console\Command;
use Storage;

class QuizExport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:export {setId}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export quz questions';

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
		$setId = $this->argument('setId');
		$headers = ['Treść pytania', 'A', 'B', 'C', 'D', 'E', 'Prawidłowa odpowiedź',
			'Rok', 'Stopień Trudność', 'Typ pytania', 'Przedmiot', 'Numer pytania'];
		$rows = collect([$headers]);


		$set = QuizSet::with(['questions.answers', 'questions.tags'])
			->where('id', $setId)
			->first();

		if (!$set) {
			$this->error('Set not found.');
			exit;
		}

		foreach ($set->questions as $question) {
			$answers = $question->answers;
			$aAnswers = $answers->toArray();
			$correct = '-';
			foreach ($aAnswers as $index => $aAnswer) {
				if ($aAnswer['is_correct']) {
					$correct = chr(65 + $index);
				}
			}
			$rows->push([
				str_replace(["\n", "\r"], '', $question->text),
				str_replace(["\n", "\r"], '', $aAnswers[0]['text']),
				str_replace(["\n", "\r"], '', $aAnswers[1]['text']),
				str_replace(["\n", "\r"], '', $aAnswers[2]['text']),
				str_replace(["\n", "\r"], '', $aAnswers[3]['text']),
				str_replace(["\n", "\r"], '', $aAnswers[4]['text']),
				$correct,
				'',
				'',
				'',
				$question->tags->pluck('name')->implode('/'),
				$question->id,
			]);
		}

		$rows = $rows->map(function ($row) {
			return implode("\t", $row);
		});

		$contents = $rows->implode("\n");
		Storage::put('exports/quiz/' . $set->name, $contents);

		return;
	}
}
