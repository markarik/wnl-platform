<?php

namespace App\Console\Commands;

use App\Models\Presentable;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Subsection;
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
	const SUBSECTIONS_DELIMITER = '*';
	const FIELDS_DELIMITER = ';';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sections:update {screen} {sections}';


	/**
	 * @var string
	 */
	protected $description = 'Update sections/subsections in a slideshow.';

	protected $screenId;

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
		$sectionsSerialized = str_replace("\n", '', $this->argument('sections'));
		$sectionsSerialized = substr_replace($sectionsSerialized, '', 0, 1);

		$screen = $this->getScreen();
		$slideshow = $this->getSlideshow($screen);
		$slides = $this->getSlides($slideshow);

		$newSectionsData = explode(self::SECTIONS_DELIMITER, $sectionsSerialized);

		if (count($newSectionsData) === 0) die("No new sections provided");

		$newSectionsData = array_map(function ($item) {
			return $this->fuckThisScript($item);

		}, $newSectionsData);

		$newSections = $this->getNewSections($newSectionsData, $slides);

		$oldSections = Section::where('screen_id', $this->screenId)->get();
		foreach ($oldSections as $oldSection) {
			$oldSection->subsections()->delete();
			$oldSection->delete();
		}

		$this->info("Old sections deleted. Now, to the new structure!");
		$this->addNewSections($newSections);

		\Artisan::call('screens:countSlides');
		Cache::tags('sections')->flush();
		Cache::tags('subsections')->flush();

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
		$subsectionSlides = $slides;
		$tableHeaders = ['Section/Subsection', 'Start', 'End', 'Length'];
		$newSections = [];
		$tableData = [];
		$offset = 0;
		for ($i = 0; $i < count($newSectionsData); $i++) {
			$newSection = [];
			$name = $newSectionsData[$i]['name'];
			$start = (int)$newSectionsData[$i]['start'];
			$end = (int)$newSectionsData[$i]['end'];
			$subsections = $newSectionsData[$i]['subsections'];

			if ($i === 0 && $start > 0) {
				$offset = $start;
				$this->info("\n Starting from slide $offset \n");
			}

			$length = $end - $start + 1;
			$newSection = [
				'name'   => $name,
				'slides' => array_splice($slides, $offset, $length),
			];

			array_push($tableData, [
				'section' => $name,
				'start'   => ($start + 1),
				'end'     => ($end + 1),
				'length'  => $length,
			]);
			foreach ($subsections as $subsection) {
				$length = $subsection['end'] - $subsection['start'] + 1;
				$newSection['subsections'][] = [
					'name'   => $subsection['name'],
					'slides' => array_splice($subsectionSlides, $offset, $length),
				];
				array_push($tableData, [
					'section' => '> ' . $subsection['name'],
					'start'   => ($subsection['start'] + 1),
					'end'     => ($subsection['end'] + 1),
					'length'  => $length,
				]);
			}
			$newSections[] = $newSection;
		}

		$slidesLeft = count($slides);
		$this->question("There are {$slidesLeft} slides left");
		$this->comment(implode(',', $slides));

		$this->table($tableHeaders, $tableData);

		$statement = 'Does it look ok? Can I replace all '
			. 'sections in this slideshow?';
		if (!$this->confirm($statement)) {
			$this->question("So go and fix it you moron!");
			die;
		}

		return $newSections;
	}

	protected function addNewSections($newSections)
	{
		$bar = $this->output->createProgressBar(count($newSections));

		foreach ($newSections as $newSection) {
			$section = Section::create([
				'name'      => $newSection['name'],
				'screen_id' => $this->screenId,
			]);

			$section->slides()->attach($newSection['slides']);

			foreach ($newSection['subsections'] as $newSubsection) {
				$subsection = Subsection::create([
					'name'      => $newSubsection['name'],
					'section_id' => $section->id,
				]);

				$subsection->slides()->attach($newSection['slides']);
			}
			$bar->advance();
		}
		$bar->finish();
	}

	protected function fuckThisScript($fuck)
	{
		$motherfucker = explode('*', $fuck);
		$section = array_shift($motherfucker);
		list($name, $start, $end) = explode(self::FIELDS_DELIMITER, $section);
		$shit = [
			'name'  => $name,
			'start' => $start,
			'end'   => $end,
		];

		$subsections = [];
		foreach ($motherfucker as $damnit) {
			list($name, $start, $end) = explode(self::FIELDS_DELIMITER, $damnit);
			$subsections[] = [
				'name'  => $name,
				'start' => $start,
				'end'   => $end,
			];
		}

		$shit['subsections'] = $subsections;

		return $shit;
	}
}
