<?php
namespace Tests\Unit;

use App\Models\Presentable;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Slideshow;
use App\Models\Subsection;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReorderSectionsTest extends TestCase
{
	use DatabaseTransactions;

	private function setupDb($slidesCount=30, $sectionsCount=3) {
		$slideshow = factory(Slideshow::class)->create();
		$screen = factory(Screen::class)->create([
			'meta' => ["resources" => [["id" => $slideshow->id, "name" => "slideshows"]], "slides_count" => $slidesCount],
		]);

		$slides = factory(Slide::class, $slidesCount)
			->create();

		$slideshow->slides()->attach($slides);

		Presentable::where([
			['presentable_type', '=', 'App\\Models\\Slideshow'],
			['presentable_id', '=', $slideshow->id],
		])->get()->each(function($presentable, $index) {
			$presentable->order_number = $index;
			$presentable->save();
		});

		$slidesInSection = $slidesCount / $sectionsCount;

		$sections = factory(Section::class, $sectionsCount)
			->create()
			->each(function($section, $index) use ($screen, $slides, $slidesInSection) {
				$section->first_slide = $index * $slidesInSection;
				$sectionSlides = $slides->splice(0, $slidesInSection);
				$section->screen()->associate($screen)->save();
				$section->slides()->attach($sectionSlides);
				$section->save();
			});

		return [$slideshow, $screen, $sections];
	}

	public function testSlidesInCorrectOrder()
	{
		list ($slideshow, $screen, $sections) = $this->setupDb();

		$sectionsNewOrder = collect([$sections->get(1), $sections->get(2), $sections->get(0)]);
		$sectionIds = $sectionsNewOrder->pluck('id')->toArray();

		Artisan::call('sections:reorder', [
			'--screen' => $screen->id,
			'sections' => $sectionIds
		]);

		foreach ($sectionIds as $index => $id) {
			$this->assertDatabaseHas('sections', [
				'id' => $id,
				'first_slide' => $index * 10
			]);
		}

		$slidesNewOrder = $sectionsNewOrder->get(0)->slides
			->concat($sectionsNewOrder->get(1)->slides)
			->concat($sectionsNewOrder->get(2)->slides);

		foreach ($slidesNewOrder as $index => $slide) {
			$this->assertDatabaseHas('presentables', [
				['presentable_type', '=', 'App\\Models\\Slideshow'],
				['presentable_id', '=', $slideshow->id],
				['slide_id', '=', $slide->id],
				['order_number', '=', $index]
			]);
		}
	}

	public function testSlidesInCorrectOrderWithSubsections()
	{
		$subsectionsCount = 6;
		$slidesCount = 30;
		$sectionsCount = 3;
		$slidesInSubsection = $subsectionsCount / 6;
		$slidesInSection = $slidesCount / $sectionsCount;

		list ($slideshow, $screen, $sections) = $this->setupDb($slidesCount, $sectionsCount);

		factory(Subsection::class, 6)->create()
			->each(function($subsection, $index) use ($slidesInSubsection, $sections) {
				$subsection->first_slide = $index * $slidesInSubsection;
				$sectionIndex = (int) floor(($index % 6) / 2);
				$section = $sections->get($sectionIndex);
				$subsection->section()->associate($section);
			});

		$sectionsNewOrder = collect([$sections->get(1), $sections->get(2), $sections->get(0)]);
		$sectionIds = $sectionsNewOrder->pluck('id')->toArray();

		Artisan::call('sections:reorder', [
			'--screen' => $screen->id,
			'sections' => $sectionIds
		]);

		foreach ($sectionsNewOrder as $index => $section) {
			$this->assertDatabaseHas('sections', [
				'id' => $section->id,
				'first_slide' => $index * $slidesInSection
			]);

			foreach ($section->subsections as $subsectionIndex => $subsection) {
				$this->assertDatabaseHas('subsections', [
					'id' => $subsection->id,
					'first_slide' => ($index * $slidesInSection) + ($subsectionIndex * $slidesInSubsection)
				]);
			}
		}
	}
}
