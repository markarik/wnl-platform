<?php

namespace App\Console\Commands;

use App\Models\Presentable;
use App\Models\Screen;
use Cache;
use Illuminate\Console\Command;


/**
 * Class SectionsUpdate
 *
 * See usage guide at:
 * https://github.com/bethinkpl/wnl-platform/wiki/Updating-sections-in-a-slideshow
 *
 * @package App\Console\Commands
 */
class SectionsUpdate extends Command
{
	const SECTIONS_DELIMITER = '#';
	const FIELDS_DELIMITER = ';';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sections:update {screen} {sections} {--subsections}';


	/**
	 * @var string
	 */
	protected $description = 'Update sections/subsections in a slideshow.';

	protected $screenId, $presentableName, $presentableType;

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
		$this->screenId = $this->argument('screen');
		$this->presentableName = $this->option('subsections') ? 'subsections' : 'sections';
		$this->presentableType = 'App\\Models\\' . studly_case(str_singular($this->presentableName));
		$sectionsSerialized = $this->argument('sections');

		$screen = $this->getScreen();
		$slideshow = $this->getSlideshow($screen);
		$slides = $this->getSlides($slideshow);

		$newSectionsData = explode(self::SECTIONS_DELIMITER, $sectionsSerialized);

		if (count($newSectionsData) === 0) die("No new $this->presentableName provided");

		$newSections = $this->getNewSections($newSectionsData, $slides);

		($this->presentableType)::where('screen_id', $this->screenId)->delete();

		$this->info("Old {$this->presentableName} deleted. Now, to the new structure!");
		$this->addNewSections($newSections);

		\Artisan::call('screens:countSlides');
		Cache::tags($this->presentableName)->flush();

		$this->info("\n\nThe end! Now go, and see what you broke.\n");

		return;
	}

	protected function getScreen()
	{
		$screen = Screen::find($this->screenId);

		if (!$screen) die("Screen with ID {$this->screenId} does not exist.\n");

		return $screen;
	}

	protected function getSlideshow($screen)
	{
		$slideshow = $screen->slideshow ?? null;

		if (!$slideshow) die("Can't find me a slideshow... :(");

		return $slideshow;
	}

	protected function getSlides($slideshow)
	{
		$slides = Presentable::where([
			['presentable_type', '=', 'App\\Models\\Slideshow'],
			['presentable_id', '=', $slideshow->id],
		])->orderBy('order_number', 'asc')->pluck('slide_id')->toArray();

		if (!$slides) die("No slides found, sorry!");

		$this->info(sprintf("\nFound %d slides\n", count($slides)));

		return $slides;
	}

	protected function getNewSections($newSectionsData, $slides)
	{
		$tableHeaders = [$this->presentableType, 'Start', 'End', 'Length'];
		$newSections = [];
		$tableData = [];
		$offset = 0;
		for ($i = 0; $i < count($newSectionsData); $i++) {
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
				'start'   => ($start + 1),
				'end'     => ($end + 1),
				'length'  => $length,
			]);
		}

		$slidesLeft = count($slides);
		$this->question("There are {$slidesLeft} slides left");
		$this->comment(implode(',', $slides));

		$this->table($tableHeaders, $tableData);

		$statement = 'Does it look ok? Can I replace all '
			. $this->presentableName
			. ' in this slideshow?';
		if (!$this->confirm($statement)) {
			$this->question("So go and fix it you moron!");
			die;
		}

		return $newSections;
	}

	protected function addNewSections($newSections)
	{
		$bar = $this->output->createProgressBar(count($newSections));

		foreach ($newSections as $name => $slides) {
			$presentable = new $this->presentableType;
			$presentable->name = $name;
			$presentable->screen_id = $this->screenId;

			$presentable->save();

			Presentable::where('presentable_type', $this->presentableType)
				->whereIn('slide_id', $slides)
				->update(['presentable_id' => $presentable->id]);

			$bar->advance();
		}

		$bar->finish();
	}
}
