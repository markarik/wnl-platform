<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use App\Models\Category;
use App\Models\QnaQuestion;
use App\Models\QuizQuestion;


class CategoriesTags extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tags:fromCategories';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create tags from categories - strip the digits';

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
		$allCategories = Category::all();

		foreach ($allCategories as $category) {
			$qnasWithTag = [];

			if (!empty($category->parent_id)) {
				$createdTag = Tag::firstOrCreate(['name' => $category->name]);

				$qnasWithTag = QnaQuestion::whereHas('tags', function($tag) use ($createdTag) {
					$tag->where('name', 'like', "$createdTag->name%");
				})->get();

				foreach($qnasWithTag as $qna) {
					if (!$qna->tags->contains($createdTag)) {
						$qna->tags()->save($createdTag);
					}
				}

				$quizQuestionsWithTag = QuizQuestion::whereHas('tags', function($tag) use ($createdTag) {
					$tag->where('name', 'like', "$createdTag->name%");
				})->get();

				foreach($quizQuestionsWithTag as $quizQuestion) {
					if (!$quizQuestion->tags->contains($createdTag)) {
						$quizQuestion->tags()->save($createdTag);
					}
				}
			}
		}
	}
}
