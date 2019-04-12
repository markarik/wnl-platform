<?php

namespace App\Console\Commands;

use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Slideshow;
use Illuminate\Console\Command;
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
	protected $signature = 'slideshow:import {lessonId} {file}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import slideshow from file and attach to lesson';

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
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function handle()
	{
		// TODO: Generate sensible filename or take if from argument.
		$fileContents = Storage::get('exports/slideshow_export.json');
		$data = json_decode($fileContents, true);

		DB::beginTransaction();
		try {

			$slideshow = Slideshow::create($data['slideshow']);
			$data['screen']['meta']['resources'][0]['id'] = $slideshow->id;
			$screen = Screen::create($data['screen']);

			$this->saveSections($data['sections'], $screen);
			$this->saveSlides($data['slides']);

			throw new \Exception('stop kurwa');

		} catch (\Exception $ex) {
			DB::rollBack();
			throw $ex;
		}

		DB::commit();
		return 0;
	}

	private function saveSections(array $sections, Screen $screen): void
	{
		$sections = array_map(function ($section) use ($screen) {
			$section['screen_id'] = $screen->id;
			return $section;
		}, $sections);
		dd($sections);
		Section::insert($sections);
	}

	private function saveSlides(array $slides): void
	{
		foreach ($slides as $slide) {
			$parser = new Parser;
			$slide['content'] = $parser->handleImages($slide['content'], true);

			Slide::create($slide);
		}
	}
}
