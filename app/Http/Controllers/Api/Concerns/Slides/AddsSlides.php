<?php

namespace App\Http\Controllers\Api\Concerns\Slides;

use App\Models\Presentable;
use App\Models\Screen;
use App\Models\Slide;
use DB;
use Illuminate\Support\Collection;

trait AddsSlides
{
	/**
	 * Increment order no. of all slides above the submitted order no.
	 *
	 * @param Collection $presentables slides section, subsection, categories
	 */
	protected function incrementOrderNumber($presentables)
	{
		foreach ($presentables as $presentable) {
			$type = addslashes($presentable->type);
			DB::statement(implode(' ', [
				"update presentables set order_number = (order_number + 1)",
				"where order_number >= {$presentable->order_number}",
				"and (presentable_type = '{$type}'",
				"and presentable_id = {$presentable->id})",
			]));
		}
	}

	/**
	 * Attach slide to presentables.
	 *
	 * @param Slide $slide
	 * @param Collection $presentables slides section, subsection, categories
	 */
	protected function attachSlide($slide, $presentables)
	{
		foreach ($presentables as $presentable) {
			$presentable->slides()->attach($slide, [
				'order_number' => $presentable->order_number,
			]);
		}
	}

	/**
	 * Get slide that currently has the given order no.
	 *
	 * @param int $slideshowId
	 * @param int $orderNumber
	 *
	 * @return mixed
	 */
	protected function getCurrentFromPresentables($slideshowId, $orderNumber)
	{
		$currentSlideId = Presentable::select(['slide_id'])
			->where('presentable_id', $slideshowId)
			->where('presentable_type', 'App\\Models\\Slideshow')
			->where('order_number', $orderNumber)
			->first()
			->slide_id;

		return Slide::find($currentSlideId);
	}

	/**
	 * Get slides section, subsection, categories etc.
	 *
	 * @param Slide $slide
	 * @param Screen $screen
	 *
	 * @return Collection
	 */
	protected function getSlidePresentables($slide, $screen)
	{
		$presentables = collect();
		$section = $slide->sections()
			->whereHas('screen', function ($query) use ($screen) {
				$query->where('id', $screen->id);
			})->first();

		if ($section) {
			$presentables->push($section);
			$subsection = $slide->subsections()
				->whereHas('section', function ($query) use ($section) {
					$query->where('id', $section->id);
				})->first();
			if ($subsection) {
				$presentables->push($subsection);
			}
		}

		$presentables = $presentables->merge($slide->categories);

		return $presentables;
	}

	/**
	 * Figure out the correct order number in each presentables group
	 *
	 * @param Slide $currentSlide
	 * @param Collection $presentables slides section, subsection, categories
	 *
	 * @return Collection
	 */
	protected function getPresentablesOrder($currentSlide, $presentables)
	{
		foreach ($presentables as $i => $presentable) {
			$type = get_class($presentable);
			$orderNumber = Presentable::select(['order_number'])
				->where('presentable_id', $presentable->id)
				->where('presentable_type', $type)
				->where('slide_id', $currentSlide->id)
				->first()
				->order_number;
			$presentables[$i]->order_number = $orderNumber;
			$presentables[$i]->type = $type;
		}

		return $presentables;
	}
}
