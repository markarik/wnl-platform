<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Presentable;
use Cache;

class SectionsUpdate extends Command
{
	const SECTIONS_DELIMITER = '#';
	const FIELDS_DELIMITER = ';';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sections:update {screen} {sections}';

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
		$screenId = $this->argument('screen');
		$sectionsSerialized = $this->argument('sections');

		$screen = Screen::find($screenId);

		if (!$screen) die("Screen with ID $screenId does not exist.\n");

		$slideshowId = $screen->slideshow->id;

		if (!$slideshowId) die("Can't find me a slideshow... :(");

		$slides = Presentable::where([
			['presentable_type', '=', 'App\\Models\\Slideshow'],
			['presentable_id', '=', $slideshowId],
		])->orderBy('order_number', 'asc')->pluck('slide_id')->toArray();

		if (!$slides) die("No slides found, sorry!");

		$this->info(sprintf("\nFound %d slides\n", count($slides)));

		$newSectionsData = explode(self::SECTIONS_DELIMITER, $sectionsSerialized);

		if (count($newSectionsData) === 0) die("No new sections provided");

		$newSections = [];

		$tableHeaders = ['Section', 'Start', 'End', 'Length'];
		$tableData = [];

		$offset = 0;
		for ($i = 0; $i<count($newSectionsData); $i++) {
			$section = explode(self::FIELDS_DELIMITER, $newSectionsData[$i]);
			$name = $section[0];
			$start = (int)$section[1];
			$end = (int)$section[2];

			if ($i === 0 && $start > 0) {
				$offset = $start;
				$this->info("\n Starting from slide $offset \n");
			}

			$length = $end - $start + 1;
			$newSections[$name] = array_splice($slides, $offset, $length);

			array_push($tableData, [
				'section' => $name,
				'start' => ($start + 1),
				'end' => ($end + 1),
				'length' => $length
			]);
		}

		$this->question('There are some slides left');
		$this->comment(implode(',', $slides));

		$this->table($tableHeaders, $tableData);

		if ($this->confirm('Does it look ok? Can I replace all sections in this slideshow?')) {
			$deletedSections = Section::where('screen_id', $screenId)->delete();

			$this->info('Old sections deleted. Now, to the new structure!');
			$bar = $this->output->createProgressBar(count($newSections));

			foreach($newSections as $name => $slides) {
				$section = new Section();
				$section->name = $name;
				$section->screen_id = $screenId;

				$section->save();

				Presentable::where('presentable_type', 'App\\Models\\Section')
					->whereIn('slide_id', $slides)
					->update(['presentable_id' => $section->id]);

				$bar->advance();
			}

			$bar->finish();

			Cache::tags('sections')->flush();

			$this->info("\n\nThe end! Now go, and see what you broke.\n");
		} else {
			$this->question("So go and fix it you moron!");
		}
	}
}
