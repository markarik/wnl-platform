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
		$slideshowsToReorder = [];

		foreach ($passedSections as $sectionId) {
			$section = Section::find($sectionId);
			$sectionScreen = $section->screen;
			$sectionSlides = $section->slides;
			$sectionPresentables = $this->getPresentableForScreen($sectionScreen);

			$sortedSlides = $sortedSlides->concat($sectionSlides);
			$firstSlideId = $this->getSlideIdFromOrderNumber($section->first_slide, $sectionPresentables);
			$sectionsFirstSlides[$firstSlideId] = $section;

			foreach($section->subsections as $subsection) {
				$subsectionFirstSlideId = $this->getSlideIdFromOrderNumber($subsection->first_slide, $sectionPresentables);
				$subsectionsFirstSlides[$subsectionFirstSlideId] = $subsection;
			}
			if ($sectionScreen->id !== $passedScreen) {
				$slideshowsToReorder[] = $sectionScreen->slideshow->id;
				$this->removeSlidesFromScreenSlideshow($sectionScreen, $sectionSlides);
			}
			$section->screen()->associate($passedScreen);
		}

		foreach($slideshowsToReorder as $slideshowId) {
			$this->call('slideshow:resetOrder', ['slideshowId' => $slideshowId]);
		}

		$passedScreenPresentables = $this->addSlidesToScreenSlideshow($screen, $sortedSlides);
		$this->setSlidesOrderNumber($passedScreenPresentables, $sortedSlides);
		$this->setFirstSlides($passedScreenPresentables, $sectionsFirstSlides);
		$this->setFirstSlides($passedScreenPresentables, $subsectionsFirstSlides);
	}

	private function removeSlidesFromScreenSlideshow($screen, $slides) {
		$screen->slideshow->slides()->detach($slides->pluck('id'));
	}

	private function addSlidesToScreenSlideshow($screen, $slides) {
		$screen->slideshow->slides()->sync($slides->pluck('id'));

		return $this->getPresentableForScreen($screen);
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
