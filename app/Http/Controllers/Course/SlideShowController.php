<?php

namespace App\Http\Controllers\Course;

use App\Models\Screen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SlideShowController extends Controller
{

	const CACHE_KEY_PREFIX = 'snippet-slideshow-';

	const CACHE_TAGS = ['slideshows', 'snippet-slideshows'];

	public function build($screenId)
	{
		$screen = Screen::find($screenId);

		if (!$screen) {
			return response('Not found', 404);
		}

		$cacheKey = self::CACHE_KEY_PREFIX . $screenId;

		if (Cache::tags(self::CACHE_TAGS)->has($cacheKey)) {
			$slides = Cache::tags(self::CACHE_TAGS)->get($cacheKey);
		} else {
			$slides = $this->fetchSlides($screen);
			Cache::tags(self::CACHE_TAGS)->forever($cacheKey, $slides);
		}

		$view = view('course.slideshow', ['slides' => $slides]);
		$view->render();

		return response($view);
	}

	/**
	 * @param $screen
	 * @return string
	 */
	public function fetchSlides($screen)
	{
		$slides = $screen->slides;
		return $slides;
	}
}
