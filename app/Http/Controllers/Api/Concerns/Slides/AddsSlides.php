<?php

namespace App\Http\Controllers\Api\Concerns\Slides;

use App\Models\Presentable;
use App\Models\Slide;
use DB;

trait AddsSlides
{
	/**
	 * Increment order no. of all slides above the submitted order no.
	 *
	 * @param $orderNumber
	 * @param $slideshow
	 * @param $section
	 * @param $subsection
	 * @param $categories
	 */
	protected function incrementOrderNumber($orderNumber, $slideshow, $section, $subsection, $categories)
	{
		$statement = [
			"update presentables set order_number = (order_number + 1)",
			"where order_number >= {$orderNumber}",
			"and ((presentable_type = 'App\\\\Models\\\\Slideshow' and presentable_id = {$slideshow->id})",
		];

		if ($section) {
			array_push($statement,
				"or (presentable_type = 'App\\\\Models\\\\Section' and presentable_id = {$section->id})"
			);
		}

		if ($subsection) {
			array_push($statement,
				"or (presentable_type = 'App\\\\Models\\\\Subsection' and presentable_id = {$subsection->id})"
			);
		}

		foreach ($categories as $category) {
			array_push($statement,
				"or (presentable_type = 'App\\\\Models\\\\Category' and presentable_id = {$category->id})"
			);
		}

		array_push($statement, ")"); // ¯\_(ツ)_/¯
		DB::statement(implode(' ', $statement));
	}

	/**
	 * Attach slide to presentables.
	 *
	 * @param $slide
	 * @param $orderNumber
	 * @param $slideshow
	 * @param $section
	 * @param $subsection
	 * @param $categories
	 */
	protected function attachSlide($slide, $orderNumber, $slideshow, $section, $subsection, $categories)
	{
		$slideshow->slides()->attach($slide, ['order_number' => $orderNumber]);

		if ($section) {
			$section->slides()->attach($slide, ['order_number' => $orderNumber]);
		}
		if ($subsection) {
			$subsection->slides()->attach($slide, ['order_number' => $orderNumber]);
		}
		foreach ($categories as $category) {
			$category->slides()->attach($slide, ['order_number' => $orderNumber]);
		}
	}


	/**
	 * Get slide that currently has the given order no.
	 *
	 * @param $slideshowId
	 * @param $orderNumber
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
	 * @param $slide
	 * @param $screen
	 *
	 * @return array
	 */
	protected function getSlidePresentables($slide, $screen)
	{
		$section = $slide->sections()
			->whereHas('screen', function ($query) use ($screen) {
				$query->where('id', $screen->id);
			})->first();

		$subsection = $slide->subsections()
			->whereHas('section', function ($query) use ($section) {
				$query->where('id', $section->id);
			})->first();

		$categories = $slide->categories;

		return [$section, $subsection, $categories];
	}
}
