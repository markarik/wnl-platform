<?php

namespace App\Http\Controllers\Course;

use App\Models\Lesson;
use App\Models\Screen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SlideShowController extends Controller
{

	public function build($screenId)
	{
		$screen = Screen::find($screenId);

		if (!$screen) {
			return response('Not found', 404);
		}

		$cacheKey = 'slideshow' . $screenId;
		$cacheTags = [
			'slideshows',
			'subject-' . $screen->lesson->subject->id,
			'subject-slideshows-' . $screen->lesson->subject->id,
		];

		if (Cache::has($cacheKey)) {
			$slides = Cache::get($cacheKey);
		} else {
			$slides = $this->fetchSlides($screen);
			Cache::tags($cacheTags)->forever($cacheKey, $slides);
		}

		return view('course.slide-show', ['slides' => $slides]);
	}

	/**
	 * @param $screen
	 * @return string
	 */
	public function fetchSlides($screen)
	{

		$slides = $screen->slides;
		dd($slides);
		return $slides;
	}
}
