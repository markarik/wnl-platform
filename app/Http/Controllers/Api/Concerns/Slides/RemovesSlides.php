<?php

namespace App\Http\Controllers\Api\Concerns\Slides;

use App\Models\Concerns\WithSlides;
use App\Models\Presentable;
use App\Models\Slide;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait RemovesSlides
{
	/**
	 * Decrements order no. of all slides above the submitted order no.
	 *
	 * @param Presentable[]|Collection $presentables
	 */
	protected function decrementOrderNumber($presentables)
	{
		foreach ($presentables as $presentable) {
			$type = addslashes($presentable->presentable_type);
			DB::statement(implode(' ', [
				"update presentables set order_number = (order_number - 1)",
				"where order_number >= {$presentable->order_number}",
				"and (presentable_type = '{$type}'",
				"and presentable_id = {$presentable->presentable_id})",
			]));
		}
	}

	/**
	 * Detach slide from presentables.
	 *
	 * @param Slide $slide
	 * @param WithSlides[] $presentables
	 */
	protected function detachSlide($slide, $presentables)
	{
		foreach ($presentables as $presentable) {
			$presentable->slides()->detach($slide);
		}
	}


	/**
	 * Get slides order number.
	 *
	 * @param Slide $slide
	 * @param Model|WithSlides $presentable
	 *
	 * @return mixed
	 */
	protected function getSlideOrderNumber($slide, $presentable)
	{
		$orderNumber = Presentable::select(['order_number'])
			->where('slide_id', $slide->id)
			->where('presentable_type', get_class($presentable))
			->where('presentable_id', $presentable->id)
			->first();

		if (is_null($orderNumber)) {
			return null;
		}
		return $orderNumber->order_number;
	}
}
