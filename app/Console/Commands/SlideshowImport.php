<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Slideshow;
use App\Models\Subsection;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lib\SlideParser\Parser;
use Storage;
use DB;

class SlideshowImport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slideshow:import {lessonId} {filename} {--dry-run}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import slideshow from file and attach to lesson';

	/**
	 * @var Collection
	 */
	private $slides;

	/**
	 * @var Collection
	 */
	private $sections;

	/**
	 * @var Collection
	 */
	private $subsections;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function handle()
	{
		// Below collections are going to serve as indices that hold
		// original IDs and newly created models. This will allow us
		// to recreate original relations.
		$this->slides = collect();
		$this->sections = collect();
		$this->subsections = collect();

		$lesson = Lesson::find($this->argument('lessonId'));
		if (is_null($lesson)) {
			$this->error('Lesson not found');
			return 1;
		}

		$filename = $this->argument('filename');
		$fileContents = Storage::drive()->get($filename);
		$data = json_decode($fileContents, true);

		DB::beginTransaction();
		try {
			$slideshow = $this->saveSlideshow($data['slideshow']);
			$screen = $this->saveScreen($data['screen'], $slideshow, $lesson);
			$this->saveSlides($data['slides']);
			$this->saveSections($data['sections'], $screen);
			$this->saveSubsections($data['subsections']);
			$this->savePresentables($data['presentables'], $slideshow);

			if ($this->option('dry-run')) {
				return 0;
			}
		} catch (\Exception $ex) {
			DB::rollBack();
			throw $ex;
		}

		DB::commit();
		return 0;
	}

	/**
	 * @param array $screenData
	 * @param Slideshow $slideshow
	 * @param Lesson $lesson
	 * @return Screen
	 */
	private function saveScreen(array $screenData, Slideshow $slideshow, Lesson $lesson): Screen
	{
		$screenData['meta']['resources'][0]['id'] = $slideshow->id;
		$screenData['lesson_id'] = $lesson->id;
		unset($screenData['id']);

		return Screen::create($screenData);
	}

	/**
	 * @param array $slideshowData
	 * @return Slideshow
	 */
	private function saveSlideshow(array $slideshowData): Slideshow
	{
		unset($slideshowData['id']);

		$parser = new Parser;
		$slideshowData['background'] = $parser->downloadBackground($slideshowData['background_url']);

		return Slideshow::create($slideshowData);
	}

	/**
	 * @param array $slides
	 */
	private function saveSlides(array $slides): void
	{
		foreach ($slides as $slide) {
			$originalSlideId = $slide['id'];
			unset($slide['id']);
			unset($slide['snippet']);
			$parser = new Parser;
			$slide['content'] = $parser->handleImages($slide['content'], true);

			$slide = Slide::create($slide);
			$this->slides->put($originalSlideId, $slide);
		}
	}

	/**
	 * @param array $sections
	 * @param Screen $screen
	 */
	private function saveSections(array $sections, Screen $screen): void
	{
		foreach ($sections as $section) {
			$originalSectionId = $section['id'];
			unset($section['id']);
			$section['screen_id'] = $screen->id;
			$section = Section::create($section);
			$this->sections->put($originalSectionId, $section);
		}
	}

	/**
	 * @param array $subsections
	 */
	private function saveSubsections(array $subsections)
	{
		foreach ($subsections as $subsection) {
			$originalSubsectionId = $subsection['id'];
			unset($subsection['id']);
			// Get ID of newly created section using its old ID.
			$subsection['section_id'] = $this->sections->get($subsection['section_id'])->id;
			$subsection = Subsection::create($subsection);
			$this->subsections->put($originalSubsectionId, $subsection);
		}
	}

	/**
	 * @param array $presentables
	 * @param Slideshow $slideshow
	 */
	private function savePresentables(array $presentables, Slideshow $slideshow): void
	{
		foreach ($presentables as $presentable) {
			unset($presentable['id']);
			// Get ID of newly created slide using its old ID.
			$presentable['slide_id'] = $this->slides->get($presentable['slide_id'])->id;
			if ($presentable['presentable_type'] === Slideshow::class) {
				$presentable['presentable_id'] = $slideshow->id;
			}
			if ($presentable['presentable_type'] === Section::class) {
				// Get ID of newly created section using its old ID.
				$presentable['presentable_id'] = $this->sections->get($presentable['presentable_id'])->id;
			}
			if ($presentable['presentable_type'] === Subsection::class) {
				// Get ID of newly created subsection using its old ID.
				$presentable['presentable_id'] = $this->subsections->get($presentable['presentable_id'])->id;
			}

			DB::table('presentables')->insert($presentable);
		}
	}
}
