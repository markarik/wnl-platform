<?php namespace App\Console\Commands;

use App\Models\QuizQuestion;
use App\Models\QuizSet;
use App\Models\Slide;
use App\Models\Tag;
use App\Models\TagsTaxonomy;
use Illuminate\Console\Command;
use Storage;

class QuizImport extends Command
{
	const VALUE_DELIMITER = "\t";
	const ROW_DELIMITER = "\r\n";
	const BASE_DIRECTORY = 'quiz';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'quiz:import {dir?} {--check} {--debug} {--addNewTags}',

		/**
		 * The console command description.
		 *
		 * @var string
		 */
		$description = 'Import quiz sets to database from storage.',
		$path,
		$globalTags = [],
		$questions;

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->path = self::BASE_DIRECTORY;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->questions = QuizQuestion::all();

		if ($subDir = $this->argument('dir')) {
			$this->path .= '/' . $subDir;
		}

		$files = Storage::disk('s3')->files($this->path);

		$this->info('Importing quiz files...');
		$bar = $this->output->createProgressBar(count($files));
		foreach ($files as $file) {
			$this->importFile($file);
			$bar->advance();
		}
		print PHP_EOL;

		if (!$files) $this->importFile($this->path);

		return;
	}

	/**
	 * Import quiz set form file.
	 *
	 * @param $file
	 */
	public function importFile($file)
	{
		$contents = Storage::disk('s3')->get($file);
		$lines = explode(self::ROW_DELIMITER, $contents);
		// Get rid of the headers
		array_shift($lines);

		$quizSet = QuizSet::firstOrcreate([
			'name' => str_replace($this->path . '/', '', $file),
		]);

		$this->globalTags = [];

		foreach ($lines as $line) {
			$this->createQuestion($line, $quizSet);
		}
	}

	public function createQuestion($line, $quizSet)
	{
		$values = explode(self::VALUE_DELIMITER, $line);
		$text = nl2br($values[0]);

		if ($qId = $values[14]) {
			$question = QuizQuestion::find($qId);
			if ($question) {
				// Line contains question id,
				// so we're just attaching it to the new set.
				$quizSet->questions()->attach($question);
				if ($this->option('addNewTags')) {
					$this->attachTags($question, $values);
					$this->tryMatchingCollectionTaxonomy($question);
				}

				return;
			}
		}

		$similarQuestion = $this->checkSimilarity($text, $values);
		if ($similarQuestion !== false) {
			if (!$quizSet->questions->contains($similarQuestion)) {
				$quizSet->questions()->attach($similarQuestion);
				$this->debug('Attached to set!');
			} else {
				$this->debug('Set already has this question');
			}

			if ($this->option('addNewTags')) {
				$this->attachTags($similarQuestion, $values);
				$this->tryMatchingCollectionTaxonomy($similarQuestion);
			}

			return;
		}

		$this->debug('Creating new question!');

		$question = $quizSet->questions()->firstOrCreate([
			'text' => $text,
		]);

		for ($i = 1; $i <= 5; $i++) {
			$hits = 0;
			$isCorrect = trim($values[6]) === chr(64 + $i);

			$question->answers()->firstOrCreate([
				'text'       => $values[$i],
				'is_correct' => $isCorrect,
				'hits'       => $hits,
			]);
		}

		$this->attachTags($question, $values);
		$this->attachSlides($question, $values);

		$this->tryMatchingCollectionTaxonomy($question);

		$question->preserve_order = (bool)$values[10];
		$question->save();
	}

	protected function attachTags($question, $values)
	{
		if (!empty($values[13])) {
			$this->globalTags[] = trim($values[13]);
		}


		$tagNames = ['LEK-' . $values[8], $values[9]];
		$tagNames = array_merge($tagNames, array_map('trim', explode('/', $values[11])));

		if (!empty($this->globalTags)) {
			$tagNames = array_merge($tagNames, $this->globalTags);
		}

		$tagNames = array_unique($tagNames);

		foreach ($tagNames as $tagName) {
			$tag = Tag::firstOrCreate(['name' => $tagName]);
			if (!$question->tags->contains($tag)) {
				$question->tags()->attach($tag);
			}
		}
	}

	protected function attachSlides($question, $values)
	{
		$slideIdsRaw = $values[12];

		if (!$slideIdsRaw) return;

		$ids = explode(',', str_replace(["\n", "\t", ' '], '', $slideIdsRaw));

		$slides = Slide::whereIn('id', $ids)->get();
		foreach ($slides as $slide) {
			if (!$question->slides->contains($slide)) {
				$question->slides()->attach($slide);
			}
		}
	}

	protected function tryMatchingCollectionTaxonomy($question)
	{
		$collectionsTagsTx = TagsTaxonomy::select()
			->whereHas('taxonomy', function ($query) {
				$query->where('name', 'collections');
			})
			->whereIn('tag_id', $question->tags->pluck('id'))
			->get();

		foreach ($collectionsTagsTx as $tagTaxonomy) {
			if ($tagTaxonomy->parent_tag_id === 0) return;

			$parentTag = Tag::find($tagTaxonomy->parent_tag_id);
			if (!$question->tags->contains($parentTag)) {
				$question->tags()->attach($parentTag);
			}
		}
	}

	protected function checkSimilarity($text, $values)
	{
		foreach ($this->questions as $question) {
			if ($question->text === $text) return $question;

			if (!$this->option('check')) continue;
			similar_text($question->text, $text, $similarity);

			if ($similarity > 78) {
				$this->warn('Similar question found! Similarity: ' . ceil($similarity) . '%');
				$this->info('Database:');
				dump($question->text . PHP_EOL);
				dump($question->answers->pluck('text')->toArray());

				$this->info('File:');
				dump($text . PHP_EOL);
				for ($i = 1; $i <= 5; $i++) {
					dump($values[$i] . PHP_EOL);
				}
				if ($this->confirm('Add as new question?')) {
					return false;
				} else {
					return $question;
				}
			}
		}

		return false;
	}

	protected function debug($message)
	{
		if ($this->option('debug')) {
			$this->info($message);
		}
	}
}
