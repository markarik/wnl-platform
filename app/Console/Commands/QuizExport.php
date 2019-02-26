<?php

namespace App\Console\Commands;

use App\Models\QuizSet;
use App\Models\QuizQuestion;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Storage;
use Symfony\Component\Console\Helper\ProgressBar;

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
	protected $description = 'Export quiz questions';

	/** @var Collection */
	private $rows;

	/** @var ProgressBar */
	private $progressBar;

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
			'Rok', 'Stopień Trudności', 'Typ pytania', 'Przedmiot', 'Numer pytania'];
		$this->rows = collect([$headers]);

		if ($setId) {
			$name = $this->processQuestionsForSet($setId, $name);
		} elseif ($tagName) {
			$name = $this->processQuestionsForTag($tagName, $name);
		} else {
			$name = $this->processAllQuestions($name);
		}

		$rows = $this->rows->map(function ($row) {
			return implode("\t", $row);
		});

		$contents = $rows->implode("\n");
		$path = 'exports/quiz/' . $name . '.tsv';

		$this->info(PHP_EOL . "Saving to ${path}");

		Storage::put($path, $contents);

		$this->info("Quiz questions saved");

		return;
	}

	public function processQuestions($questions)
	{
		if (!$questions) {
			$this->error('No questions found.');
			exit;
		}

		/** @var QuizQuestion $question */
		foreach ($questions as $question) {
			$answers = $question->quizAnswers;
			$aAnswers = $answers->toArray();

			$correct = '-';
			foreach ($aAnswers as $index => $aAnswer) {
				if ($aAnswer['is_correct']) {
					$correct = chr(65 + $index);
				}
			}
			$this->rows->push([
				str_replace(["\n", "\r"], '', $question->text),
				isset($aAnswers[0]) ? str_replace(["\n", "\r"], '', $aAnswers[0]['text']) : '',
				isset($aAnswers[1]) ? str_replace(["\n", "\r"], '', $aAnswers[1]['text']) : '',
				isset($aAnswers[2]) ? str_replace(["\n", "\r"], '', $aAnswers[2]['text']) : '',
				isset($aAnswers[3]) ? str_replace(["\n", "\r"], '', $aAnswers[3]['text']) : '',
				isset($aAnswers[4]) ? str_replace(["\n", "\r"], '', $aAnswers[4]['text']) : '',
				$correct,
				'',
				'',
				'',
				$question->tags->pluck('name')->implode('/'),
				$question->id,
			]);

			$this->progressBar->advance(1);
		}

	}

	private function setupProgressBar($count)
	{
		$this->progressBar = $this->output->createProgressBar($count);
	}

	private function processQuestionsForSet($setId, string $name): string
	{
		$set = QuizSet::with(['questions.quizAnswers', 'questions.tags'])
			->where('id', $setId)
			->first();

		if (!$set) {
			$this->error('Set not found.');
			exit;
		}

		$name .= $set->name;

		$questions = $set->questions;
		$this->setupProgressBar($questions->count());
		$this->processQuestions($questions);

		return $name;
	}

	private function processQuestionsForTag(string $tagName, string $name): string
	{
		$tag = Tag::where('name', $tagName)->first();
		if (!$tag) {
			$this->error('Tag not found.');
			exit;
		}

		$name .= $tagName;

		$builder = QuizQuestion::whereHas('tags', function ($query) use ($tag) {
			$query->where('tags.id', $tag->id);
		});
		$this->setupProgressBar($builder->count());
		$builder->chunk(100, [$this, 'processQuestions']);

		return $name;
	}

	private function processAllQuestions(string $name): string
	{
		$name .= 'all';

		$this->setupProgressBar(QuizQuestion::count());
		QuizQuestion::chunk(100, [$this, 'processQuestions']);
		return $name;
	}
}
