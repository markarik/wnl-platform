<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use App\Models\Taxonomy;
use App\Models\QuizQuestion;

class TagsFromTaxonomies extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tags:fromTaxonomies';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create tags from taxonomies - add parent tags to all items having their children tags.';

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
		$taxonomies = Taxonomy::all();
		$progressTaxonomies = $this->output->createProgressBar(count($taxonomies));
		$progressTaxonomies->setMessage('Taxonomies');

		foreach ($taxonomies as $taxonomy) {
			$children = $taxonomy->tagsTaxonomy()->where('parent_tag_id', '!=', 0)->get();
			$progressChildren = $this->output->createProgressBar(count($children));
			$progressChildren->setMessage('Child tags');

			foreach ($children as $child) {
				$tagModel = Tag::find($child->parent_tag_id);
				/** @var QuizQuestion[] $questionsToTag */
				$questionsToTag = QuizQuestion::whereHas('tags', function($query) use ($child) {
					$query->where('tags.id', $child->tag_id);
				})->whereDoesntHave('tags', function ($query) use ($child) {
					$query->where('tags.id', $child->parent_tag_id);
				})->get();

				$progressQuestions = $this->output->createProgressBar(count($questionsToTag));
				$progressQuestions->setMessage('Questions');

				foreach ($questionsToTag as $question) {
					$question->tags()->attach($tagModel);
					$progressQuestions->advance();
				}

				$progressChildren->advance();
			}

			$progressTaxonomies->advance();
		}
	}
}
