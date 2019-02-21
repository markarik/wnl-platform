<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tag;
use App\Models\Category;
use App\Models\QnaQuestion;
use App\Models\QuizQuestion;
use App\Models\Screen;
use App\Models\Slide;
use App\Models\Lesson;

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
		/** @var Category[] $allCategories */
		$allCategories = Category::all();

		foreach ($allCategories as $category) {
			if (!empty($category->parent_id)) {
				$createdTag = Tag::firstOrCreate(['name' => $category->name]);

				$qnasWithTag = QnaQuestion::whereHas('tags', function($tag) use ($createdTag) {
					$tag->where('name', 'like', "$createdTag->name%");
				})->get();

				foreach($qnasWithTag as $qna) {
					if (!$qna->tags->contains($createdTag)) {
						echo(sprintf("Adding tag: %s to qnaQuestion with id: %s \n", $createdTag->name, $qna->id));
						$qna->tags()->save($createdTag);
					}
				}

				$quizQuestionsWithTag = QuizQuestion::whereHas('tags', function($tag) use ($createdTag) {
					$tag->where('name', 'like', "$createdTag->name%");
				})->get();

				foreach($quizQuestionsWithTag as $quizQuestion) {
					if (!$quizQuestion->tags->contains($createdTag)) {
						echo(sprintf("Adding tag: %s to quizQuestion with id: %s \n", $createdTag->name, $quizQuestion->id));
						$quizQuestion->tags()->save($createdTag);
					}
				}

				$screensWithTags = Screen::whereHas('tags', function($tag) use ($createdTag) {
					$tag->where('name', 'like', "$createdTag->name%");
				})->get();

				foreach($screensWithTags as $screen) {
					if (!$screen->tags->contains($createdTag)) {
						echo(sprintf("Adding tag: %s to screen with id: %s \n", $createdTag->name, $screen->id));
						$screen->tags()->save($createdTag);
					}
				}

				$screensWithTags = Screen::whereHas('tags', function($tag) use ($createdTag) {
					$tag->where('name', 'like', "$createdTag->name%");
				})->get();

				foreach($screensWithTags as $screen) {
					if (!$screen->tags->contains($createdTag)) {
						echo(sprintf("Adding tag: %s to screen with id: %s \n", $createdTag->name, $screen->id));
						$screen->tags()->save($createdTag);
					}
				}

				$lessonsWithTags = Lesson::where('name', 'like', "$createdTag->name%")->get();

				foreach($lessonsWithTags as $lesson) {
					if (!$lesson->tags->contains($createdTag)) {
						echo(sprintf("Adding tag: %s to lesson with id: %s and name: %s \n", $createdTag->name, $lesson->id, $lesson->name));
						$lesson->tags()->save($createdTag);
					}
				}

				$slidesWithTags = Slide::whereHas('tags', function($tag) use ($createdTag) {
					$tag->where('name', 'like', "$createdTag->name%");
				})->get();

				foreach($slidesWithTags as $slide) {
					if (!$slide->tags->contains($createdTag)) {
						echo(sprintf("Adding tag: %s to slide with id: %s \n", $createdTag->name, $slide->id));
						$slide->tags()->save($createdTag);
					}
				}
			}
		}
	}
}
