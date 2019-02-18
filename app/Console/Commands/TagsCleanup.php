<?php

namespace App\Console\Commands;

use Cache;
use Illuminate\Console\Command;
use App\Models\Tag;

class TagsCleanup extends Command
{
	const VALUE_DELIMITER = "\t";
	const ROW_DELIMITER = "\r\n";

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tags:cleanup {filename}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Cleans-up tags according to a formated .tsv spreadsheet.';

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
		$source = \Storage::drive()->get($this->argument('filename'));
		$rows = explode(self::ROW_DELIMITER, $source);

		if (!$rows) {
			$this->info('No tags found, sorry mate!');
			return;
		}

		foreach ($rows as $row) {
			$fields = explode(self::VALUE_DELIMITER, $row);

			if (count($fields) < 2) {
				$this->warn("No fields? Don't waste my time! (┛◉Д◉)┛彡┻━┻");
				continue;
			}

			$id = $fields[0];
			$name = $fields[1];
			$trimmed = $fields[2];
			$command = $fields[3];
			$commandTarget = '';

			if (count($fields) > 4) {
				$commandTarget = $fields[4];
			}

			// First, fetch the tag object
			$tag = Tag::find($id);

			if (!$tag) {
				$this->warn("Tag {$id} - {$trimmed} not found! (◕︵◕)");
				continue;
			}
			$isValid = $tag->name === $name;

			if (!$isValid) {
				$this->warn("Name of the {$tag->name} tag does not match source value {$name}! ಠ益ಠ");
				continue;
			}

			switch ($command) {
				case 1:
					$this->info("Trimming tag {$name} to {$trimmed}");
					$tag->name = $trimmed;
					$tag->save();
					continue;
				case 2:
					$this->replace($tag, $commandTarget);
					continue;
				case 3:
					$this->info("Updating tag {$name} to {$commandTarget}");
					$tag->name = $commandTarget;
					$tag->save();
					continue;
				case 4:
					$this->remove($tag);
					continue;
			}
		}
	}

	private function replace(Tag $tag, $newId) {
		$newTag = Tag::find($newId);

		if (!$newTag) {
			$this->error("Tag of ID {$newId} that were to replace {$tag->name} does not exist! (┛◉Д◉)┛彡┻━┻");
			return;
		}

		if ($this->confirm("Should I replace tag {$tag->name} with {$newTag->name}? ( ಠ◡ಠ )")) {
			$sql = \DB::table('taggables')
				->where('tag_id', $tag->id)
				->update(['tag_id' => $newTag->id]);
			$tag->delete();
		}
	}

	private function remove(Tag $tag) {
		if ($this->confirm("Should I REMOVE tag {$tag->id} with {$tag->name}? ( ಠ◡ಠ )")) {
			$sql = \DB::table('taggables')
				->where('tag_id', $tag->id)
				->delete();
			$tag->delete();
		}
	}
}
