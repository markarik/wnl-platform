<?php

namespace App\Console\Commands;

use App\Models\Flashcard;
use App\Models\UserFlashcardsResults;
use Illuminate\Console\Command;

class JoinFlashcards extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'flashcards:join-flashcards';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Join flashcards with the same content';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$flashcards = \DB::select("select min(id) as id, count(1) from flashcards group by content having count(1) > 1");

		$bar = $this->output->createProgressBar(count($flashcards));

		foreach ($flashcards as $flashcardRow) {
			$flashcard = Flashcard::find($flashcardRow->id);

			if (empty($flashcard)) {
				$this->output->warning("Flashcard with {$flashcardRow->id} not found!");
				continue;
			}
			$bar->advance();

			$matchingFlashcards = Flashcard
				::where('content', $flashcard->content)
				->where('id', '<>', $flashcard->id)
				->get();

			if ($matchingFlashcards->count() === 0) {
				$this->output->text("\n Matching flashcards not found.... \n");
				continue;
			}

			$matchingFlashcardsIds = $matchingFlashcards->pluck('id')->toArray();

			$this->transaction(function () use ($matchingFlashcardsIds, $flashcard) {
				\DB::table('flashcards_set_flashcard')
					->whereIn('flashcard_id', $matchingFlashcardsIds)
					->update(['flashcard_id' => $flashcard->id]);

				UserFlashcardsResults
					::whereIn('flashcard_id', $matchingFlashcardsIds)
					->update(['flashcard_id' => $flashcard->id]);

				Flashcard::destroy($matchingFlashcardsIds);
			});
		}

		$bar->finish();
	}

	private function transaction(\Closure $callback)
	{
		\DB::beginTransaction();

		try {
			$callback();
		} catch (Exception $e) {
			\DB::rollBack();
			throw $e;
		}

		\DB::commit();
	}
}
