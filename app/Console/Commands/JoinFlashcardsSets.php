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
		$flashcardsSets = FlashcardsSet::all()->pluck('id')->toArray();
		$bar = $this->output->createProgressBar(count($flashcardsSets));

		foreach ($flashcardsSets as $setId) {
			$bar->advance();

			$set = FlashcardsSet::find($setId);

			if (empty($set)) {
				$this->output->text("Set already merged...");
				continue;
			}

			$matchingSetByName = FlashcardsSet
				::where('name', $set->name)
				->where('id', '<>', $set->id)
				->get();

			if ($matchingSetByName->count() === 0) {
				$this->output->text("Set does not have matching sets...");
				continue;
			}

			foreach ($matchingSetByName as $matchingSet) {
				if ($matchingSet->flashcards->diff($set->flashcards)->count() !== 0) {
					$this->output->text("Set has matching name but flashcards are different...");
					continue;
				}

				$screens = \DB::select(
					"select id from (select id, JSON_EXTRACT(meta, '$.resources[*].id') as sets_ids from screens where type = 'flashcards') as meta where JSON_CONTAINS(sets_ids, '{$matchingSet->id}')"
				);

				if (count($screens) > 1) {
					$this->output->note("more than one screen found for set $set->id \n");
					continue;
				}

				$screen = $screens[0];
				$screen = Screen::find($screen->id);
				$mappedMeta = array_map(function($resource) use ($matchingSet, $set) {
					if ($resource['id'] == $matchingSet->id) {
						$resource['id'] = $set->id;
					}

					return $resource;
				}, $screen->meta['resources']);

				$screen->meta = array_merge($screen->meta, [
					'resources' => $mappedMeta
				]);

				$screen->save();

				\DB::table('flashcards_set_flashcard')
					->where('flashcard_set_id', $matchingSet->id)
					->update(['flashcard_set_id' => $set->id]);

				FlashcardsSet::destroy($matchingSet->id);
			}
		}

		$bar->finish();
	}
}
