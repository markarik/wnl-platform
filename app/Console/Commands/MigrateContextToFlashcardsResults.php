<?php

namespace App\Console\Commands;

use App\Models\FlashcardsSet;
use App\Models\UserFlashcardsResults;
use Illuminate\Console\Command;

class MigrateContextToFlashcardsResults extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'flashcards:migrate-context';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add context to user flashcards results';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$sets = FlashcardsSet::all();
		$bar = $this->output->createProgressBar($sets->count());

		foreach ($sets as $set) {
			$screens = \DB::select(
				"select id from (select id, JSON_EXTRACT(meta, '$.resources[*].id') as sets_ids from screens where type = 'flashcards') as meta where JSON_CONTAINS(sets_ids, '{$set->id}')"
			);

			if (count($screens) > 1) {
				$this->output->note("more than one screen found for set $set->id");
				continue;
			}

			$screenId = $screens[0];
			$flashcards = $set->flashcards()->select('flashcard_id')->pluck('flashcard_id')->toArray();

			UserFlashcardsResults::whereIn('flashcard_id', $flashcards)->update([
				'context_type' => 'App\\Models\\Screen', 'context_id' => $screenId->id
			]);

			$bar->advance();
		}

		$bar->finish();
	}
}
