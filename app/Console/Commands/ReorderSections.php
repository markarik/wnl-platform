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
		$presentables = $this->getPresentableForScreen($screen);

		$sortedSlides = collect();
		$sectionsFirstSlides = []; // slideId => sectionId
		$subsectionsFirstSlides = []; // slideId => subsectionId

		foreach ($passedSections as $sectionId) {
			$section = Section::find($sectionId);
			$sectionSlides = $section->slides;

			$sortedSlides = $sortedSlides->concat($sectionSlides);
			$firstSlideId = $this->getSlideIdFromOrderNumber($section->first_slide, $presentables);
			$sectionsFirstSlides[$firstSlideId] = $section;

			foreach($section->subsections as $subsection) {
				$subsectionFirstSlideId = $this->getSlideIdFromOrderNumber($subsection->first_slide, $presentables);
				$subsectionsFirstSlides[$subsectionFirstSlideId] = $subsection;
			}
		}

		$this->setSlidesOrderNumber($presentables, $sortedSlides);
		$this->setFirstSlides($presentables, $sectionsFirstSlides);
		$this->setFirstSlides($presentables, $subsectionsFirstSlides);
	}

	private function getPresentableForScreen($screen) {
		$whereClause = [
			['presentable_type', 'App\\Models\\Slideshow'],
			['presentable_id', '=', (int) $screen->slideshow->id],
		];

		return Presentable::where($whereClause)->get();
	}

	private function getSlideIdFromOrderNumber($orderNumber, $presentables) {
		$firstSlide = $presentables->first(function($presentable) use ($orderNumber) {
			return $presentable->order_number === $orderNumber;
		});

		return $firstSlide->slide_id;
	}

	private function setSlidesOrderNumber($presentables, $slides) {
		foreach ($slides as $index => $slide) {
			$presentable = $presentables->first(function($presentable) use ($slide) {
				return $presentable->slide_id === $slide->id;
			});

			$presentable->order_number = $index;
			$presentable->save();
		}
	}

	private function setFirstSlides($presentables, $firstSlides) {
		// item is either section or subsection
		foreach ($firstSlides as $slideId => $item) {
			$presentable = $presentables->first(function($item) use($slideId) {
				return $item->slide_id === $slideId;
			});
			$item->first_slide = $presentable->order_number;
			$item->save();
		}
	}
}
