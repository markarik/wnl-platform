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
		$flashcards = Flashcard::all()->pluck('id')->toArray();
		$bar = $this->output->createProgressBar(count($flashcards));

		foreach ($flashcards as $flashcardId) {
			$bar->advance();
			$flashcard = Flashcard::find($flashcardId);

			if (empty($flashcard)) {
				$this->output->text("\n Flashcard already merged.... \n");
				continue;
			}

			$matchingFlashcards = Flashcard
				::where('content', $flashcard->content)
				->where('id', '<>', $flashcardId)
				->get();

			if ($matchingFlashcards->count() === 0) {
				$this->output->text("\n Matching flashcards not found.... \n");
				continue;
			}

			$matchingFlashcardsIds = $matchingFlashcards->pluck('id')->toArray();

			\DB::table('flashcards_set_flashcard')
				->whereIn('flashcard_id', $matchingFlashcardsIds)
				->update(['flashcard_id' => $flashcardId]);

			UserFlashcardsResults
				::whereIn('flashcard_id', $matchingFlashcardsIds)
				->update(['flashcard_id' => $flashcardId]);

			Flashcard::destroy($matchingFlashcardsIds);
		}

		$bar->finish();
	}
}
