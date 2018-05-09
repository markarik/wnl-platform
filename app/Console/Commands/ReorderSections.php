<?php

namespace App\Console\Commands;

use App\Models\Presentable;
use App\Models\Screen;
use App\Models\Section;
use Illuminate\Console\Command;

class ReorderSections extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sections:reorder {--screen=} {sections*}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Order screen sections.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$passedScreen = $this->option('screen');
		$passedSections = $this->argument('sections');

		$screen = Screen::find($passedScreen);

		$sortedSlides = collect();
		$sectionsFirstSlides = []; // slideId => sectionId
		$subsectionsFirstSlides = []; // slideId => subsectionId

		foreach ($passedSections as $sectionId) {
			$section = Section::find($sectionId);
			$sectionSlides = $section->slides;

			$sortedSlides = $sortedSlides->concat($sectionSlides);
			$firstSlideId = $this->getSlideIdFromOrderNumber($section->first_slide, $section);
			$sectionsFirstSlides[$firstSlideId] = $section;

			foreach($section->subsections as $subsection) {
				$subsectionFirstSlideId = $this->getSlideIdFromOrderNumber($subsection->first_slide, $section);
				$subsectionsFirstSlides[$subsectionFirstSlideId] = $subsection;
			}
		}

		$slideshowId = $screen->slideshow->id;
		$presentable = $this->setSlidesOrderNumber($slideshowId, $sortedSlides);
		$this->setFirstSlides($presentable, $sectionsFirstSlides);
		$this->setFirstSlides($presentable, $subsectionsFirstSlides);
	}

	private function getSlideIdFromOrderNumber($orderNumber, $section) {
		$slideshow = $section->screen->slideshow;

		$firstSlide = Presentable::where([
			['presentable_type', '=', 'App\\Models\\Slideshow'],
			['presentable_id', '=', $slideshow->id],
			['order_number', '=', $orderNumber],
		])->first();

		return $firstSlide->slide_id;
	}

	private function setSlidesOrderNumber($slideshowId, $slides) {
		$whereClause = [
			['presentable_type', 'App\\Models\\Slideshow'],
			['presentable_id', '=', (int) $slideshowId],
		];

		foreach ($slides as $index => $slide) {
			$presentable = Presentable::where($whereClause)
				->where('slide_id', $slide->id)
				->first();

			$presentable->order_number = $index;
			$presentable->save();
		}

		\Cache::tags(json_encode(['where' => $whereClause]))->flush();

		return Presentable::where($whereClause)->get();
	}

	private function setFirstSlides($presentable, $firstSlides) {
		// item is either section or subsection
		foreach ($firstSlides as $slideId => $item) {
			$slideFromPresentable = $presentable->first(function($item) use($slideId) {
				return $item->slide_id === $slideId;
			});
			$item->first_slide = $slideFromPresentable->order_number;
			$item->save();
		}
	}
}
