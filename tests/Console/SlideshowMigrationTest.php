<?php


namespace Tests\Console;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Lesson;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Slideshow;
use App\Models\Subsection;
use Tests\TestCase;
use Storage;

class SlideshowMigrationTest extends TestCase
{
	use DatabaseTransactions;

	const SLIDES_COUNT = 300;
	const SECTIONS_COUNT = 10;
	const SLIDES_PER_SECTION = 30;
	const SUBSECTIONS_PER_SECTION = 5;
	const SLIDES_PER_SUBSECTION = 6;

	public function setUp()
	{
		parent::setUp();
		Storage::fake('test_storage');
	}

	public function testSlideshowMigration()
	{
		/*
		 * Create mock slideshow
		 */
		$originalSlideshow = factory(Slideshow::class)->create();
		$originalScreen = factory(Screen::class)->create([
			'meta' => [
				'resources' => [
					[
						'name' => 'slideshows',
						'id' => $originalSlideshow->id,
					]
				]
			]
		]);
		$originalSlides = factory(Slide::class, self::SLIDES_COUNT)->create();
		$originalSections = factory(Section::class, self::SECTIONS_COUNT)->create([
			'screen_id' => $originalScreen->id,
		]);
		$chunkedSlides = $originalSlides->chunk(self::SLIDES_PER_SECTION);
		foreach ($originalSections as $section) {
			$sectionSlides = $chunkedSlides->pop();
			$section->slides()->attach($sectionSlides);
			$subsections = factory(Subsection::class, self::SUBSECTIONS_PER_SECTION)->create([
				'section_id' => $section->id,
			]);
			$subsectionSlides = $sectionSlides->chunk(self::SLIDES_PER_SUBSECTION);
			foreach ($subsections as $subsection) {
				$subsection->slides()->attach($subsectionSlides->pop());
			}
		}

		$originalSlideshow->slides()->attach($originalSlides);

		/*
		 * Export slideshow
		 */
		$filename = 'slideshow_export';
		$path = 'exports/' . $filename . '.json';

		$this->artisan('slideshow:export', [
			'screenId' => $originalScreen->id,
			'filename' => $filename
		])->assertExitCode(0);

		Storage::disk('test_storage')->assertExists($path);

		/*
		 * Import slideshow
		 */
		$lesson = factory(Lesson::class)->create();
		$this->artisan('slideshow:import', [
			'lessonId' => $lesson->id,
			'filePath' => $path
		])->assertExitCode(0);

		// assert lesson has a new screen
		$screen = $lesson->screens()->where('type', 'slideshow')->first();
		$this->assertNotNull($screen);

		// assert screen has slideshow
		$slideshow = $screen->slideshow;
		$this->assertNotNull($slideshow);

		// assert number of slides
		$slides = $slideshow->slides;
		$this->assertEquals($slides->count(), self::SLIDES_COUNT);

		// assert sections
		$sections = $screen->sections;
		$this->assertEquals($sections->count(), self::SECTIONS_COUNT);

		// assert sections slides
		foreach ($sections as $section) {
			$this->assertEquals($section->slides->count(), self::SLIDES_PER_SECTION);
		}

		// assert subsections
		foreach ($sections as $section) {
			$this->assertEquals($section->subsections->count(), self::SUBSECTIONS_PER_SECTION);
		}

		// assert subsections slides
		$subsections = Subsection::whereIn('section_id', $sections->pluck('id'))->get();
		foreach ($subsections as $subsection) {
			$this->assertEquals($subsection->slides->count(), self::SLIDES_PER_SUBSECTION);
		}
	}
}
