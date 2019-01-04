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
		$qnaQuestions = QnaQuestion::get();

		$bar = $this->output->createProgressBar($qnaQuestions->count());
		$bar->start();

		foreach ($qnaQuestions as $qnaQuestion) {
			$bar->advance();
			$qnaQuestion = QnaQuestion::find($qnaQuestion->id);

			if (!empty($qnaQuestion->discussion_id)) {
				// if question is already attached to discussion, skip it
				continue;
			}

			$tags = $qnaQuestion->tags->pluck('id');
			if ($tags->count() === 0) {
				$this->output->warning("Qna Question without tags: {$qnaQuestion->id}");
				continue;
			}

			$qnaQuestionsQuery = QnaQuestion::select();

			foreach ($tags as $tagId) {
				$qnaQuestionsQuery->whereHas('tags', function ($query) use ($tagId) {
					$query->where('tags.id', $tagId);
				});
			}

			$matchingQnaQuestions = $qnaQuestionsQuery->get();

			$screensQuery = Screen::select();
			foreach ($tags as $tagId) {
				$screensQuery->whereHas('tags', function ($query) use ($tagId) {
					$query->where('tags.id', $tagId);
				});
			}
			$name = '';
			$matchingDiscussable = $screensQuery->first();

			if (empty($matchingDiscussable)) {
				// qna questions doesn't have matching screen - check page
				$pagesQuery = Page::select();
				foreach ($tags as $tagId) {
					$pagesQuery->whereHas('tags', function ($query) use ($tagId) {
						$query->where('tags.id', $tagId);
					});
				}

				$matchingDiscussable = $pagesQuery->first();

				if (empty($matchingDiscussable)) {
					continue;
				} else {
					$name = "{$matchingDiscussable->slug}";
				}
			} else {
				// qna questions have matching screen
				$name = "{$matchingDiscussable->lesson->name} - {$matchingDiscussable->name}";
			}

			$discussion = Discussion::create([
				'name' => $name
			]);
			$discussion->questions()->saveMany($matchingQnaQuestions);

			$matchingDiscussable->discussion()->associate($discussion);
			$matchingDiscussable->is_discussable = true;
			$matchingDiscussable->save();
		}

		$bar->finish();
	}
}
