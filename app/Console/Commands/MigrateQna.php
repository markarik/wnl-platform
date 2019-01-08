<?php

namespace App\Console\Commands;

use App\Models\Discussion;
use App\Models\Page;
use App\Models\QnaQuestion;
use App\Models\Screen;
use Illuminate\Console\Command;

class MigrateQna extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'data-migration:attach-qna-to-discussion';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "PLAT-865 - for each QnA tags combination create a dedicated discussion and attach QnA to it.
	Created discussions are attached to matching screen or page.";

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$screens = Screen::has('tags')
			->get();

		$pages = Page::all();

		$bar = $this->output->createProgressBar($screens->count() + $pages->count());
		$bar->start();

		foreach ($screens as $screen) {
			$name = "{$screen->lesson->name} - {$screen->name}";

			$this->attachDiscussion($screen, $name);

			$bar->advance();
		}

		foreach ($pages as $page) {
			$name = $page->slug;

			$this->attachDiscussion($page, $name);

			$bar->advance();

		}

		$bar->finish();
	}

	private function attachDiscussion($discussable, $discussionName) {
		$tags = $discussable->tags->pluck('id');

		$qnaQuestionsQuery = QnaQuestion::select();

		foreach ($tags as $tagId) {
			$qnaQuestionsQuery->whereHas('tags', function ($query) use ($tagId) {
				$query->where('tags.id', $tagId);
			});
		}

		$matchingQnaQuestions = $qnaQuestionsQuery->get();

		$discussion = Discussion::create([
			'name' => $discussionName
		]);
		$discussion->questions()->saveMany($matchingQnaQuestions);

		$discussable->discussion()->associate($discussion);
		$discussable->is_discussable = true;
		$discussable->save();
	}
}
