<?php
namespace Tests\Unit;

use App\Models\Presentable;
use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Slideshow;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReorderSectionsTest extends TestCase
{
	use DatabaseTransactions;

	public function testSlidesInCorrectOrder()
	{
		$slideshow = factory(Slideshow::class)->create();
		$screen = factory(Screen::class)->create([
			'meta' => ["resources" => [["id" => $slideshow->id, "name" => "slideshows"]], "slides_count" => 30],
		]);

		$slides = factory(Slide::class, 30)
			->create();

		$slideshow->slides()->attach($slides);

		Presentable::where([
			['presentable_type', '=', 'App\\Models\\Slideshow'],
			['presentable_id', '=', $slideshow->id],
		])->get()->each(function($presentable, $index) {
			$presentable->order_number = $index;
			$presentable->save();
		});

		$sections = factory(Section::class, 3)
			->create()
			->each(function($section, $index) use ($screen, $slides) {
				$section->first_slide = $index * 10;
				$sectionSlides = $slides->splice(0, 10);
				$section->screen()->associate($screen)->save();
				$section->slides()->attach($sectionSlides);
				$section->save();
			});

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
}
