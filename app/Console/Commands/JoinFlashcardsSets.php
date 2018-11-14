<?php

namespace App\Console\Commands;

use App\Models\FlashcardsSet;
use App\Models\Screen;
use Illuminate\Console\Command;

class JoinFlashcardsSets extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'flashcards:join-flashcards-sets';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Join flashcards sets with the same flashcards';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$flashcardsSets = FlashcardsSet::all()->pluck('id')->toArray();
		$bar = $this->output->createProgressBar(count($flashcardsSets));
		$differentFlashcards = [];
		$mergedFlashcards = [];

		foreach ($flashcardsSets as $setId) {
			$bar->advance();

			$set = FlashcardsSet::find($setId);

			if (empty($set)) {
				$this->output->text("\n Set already merged... \n");
				continue;
			}

			$matchingSetByName = FlashcardsSet
				::where('name', $set->name)
				->where('id', '<>', $set->id)
				->get();

			if ($matchingSetByName->count() === 0) {
				$this->output->text("\n Set does not have matching sets... \n");
				continue;
			}

			foreach ($matchingSetByName as $matchingSet) {
				$matchingSetFlashcardsDiff = $matchingSet->flashcards->diff($set->flashcards);
				if ($matchingSetFlashcardsDiff->count() > 0 && $matchingSetFlashcardsDiff->count() < 6) {
					$orderNumber = $set->flashcards->count();
					$flashcardsToMove = [];

					foreach($matchingSetFlashcardsDiff as $flashcardToMove) {
						$flashcardsToMove[$flashcardToMove->id] = ['order_number' => $orderNumber++];
					}

					$set->flashcards()->syncWithoutDetaching($flashcardsToMove);
					$mergedFlashcards[] = [$set->name];
				} else if ($matchingSetFlashcardsDiff->count() > 5) {
					$this->output->text("\n Set has matching name but flashcards are different...");
					$differentFlashcards[] = [$set->name];
					continue;
				}

				$screens = \DB::select(
					"select id from (select id, JSON_EXTRACT(meta, '$.resources[*].id') as sets_ids from screens where type = 'flashcards') as meta where JSON_CONTAINS(sets_ids, '{$matchingSet->id}')"
				);

				foreach ($screens as $screen) {
					$this->updateScreen($screen, $matchingSet->id, $set->id);
				}

				\DB::table('flashcards_set_flashcard')
					->where('flashcard_set_id', $matchingSet->id)
					->delete();

				FlashcardsSet::destroy($matchingSet->id);
			}
		}

		echo PHP_EOL;
		$this->info("Different flashcards - not merged");
		$this->table(['set name'], $differentFlashcards);
		echo PHP_EOL;
		$this->info("Merged flashcards");
		$this->table(['set name'], $mergedFlashcards);
		$bar->finish();
	}

	protected function updateScreen($screen, $matchingSetId, $setId) {
		$screen = Screen::find($screen->id);
		$mappedMeta = array_map(function($resource) use ($matchingSetId, $setId) {
			if ($resource['id'] == $matchingSetId) {
				$resource['id'] = $setId;
			}

			return $resource;
		}, $screen->meta['resources']);

		$screen->meta = array_merge($screen->meta, [
			'resources' => $mappedMeta
		]);

		$screen->save();
	}
}
