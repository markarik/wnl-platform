<?php

namespace App\Console\Commands;

use App\Models\QuizSet;
use App\Models\QuizQuestion;
use App\Models\Tag;
use Illuminate\Console\Command;
use Storage;

class QuizExport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:export {--S|setId=} {--T|tag=}';

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
		$setId = $this->option('setId');
		$tagName = trim($this->option('tag'));
		$name = 'QuizQuestions-';

		$headers = ['Treść pytania', 'A', 'B', 'C', 'D', 'E', 'Prawidłowa odpowiedź',
			'Rok', 'Stopień Trudność', 'Typ pytania', 'Przedmiot', 'Numer pytania'];
		$rows = collect([$headers]);

		if ($setId) {
			$set = QuizSet::with(['questions.answers', 'questions.tags'])
			->where('id', $setId)
			->first();

			if (!$set) {
				$this->error('Set not found.');
				exit;
			}

			$name .= $set->name;
			$questions = $set->questions;
		} elseif ($tagName) {
			$tag = Tag::firstOr(
				['name' => $tagName],
				function () {
					$this->error('Tag not found.');
					exit;
				}
			);

			$name .= $tagName;
			$questions = QuizQuestion::whereHas('tags', function ($query) use ($tag) {
				$query->whereIn('tags.id', $tag->id);
			})->get();
		} else {
			$this->error('Either a set ID or tag is required.');
			exit;
		}

		if (!$questions) {
			$this->error('No questions found.');
			exit;
		}

		foreach ($questions as $question) {
			$answers = $question->quizAnswers;
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
		Storage::put('exports/quiz/' . $name . '.tsv', $contents);

		return;
	}
}
