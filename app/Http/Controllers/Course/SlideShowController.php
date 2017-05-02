<?php

namespace App\Http\Controllers\Course;

use App\Models\Screen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SlideShowController extends Controller
{

	const CACHE_KEY_PREFIX = 'screens-slideshows';
	const CACHE_VERSION = 2;
	const CACHE_TAGS = ['slideshows', 'screens-slideshows'];

	public function build($screenId)
	{
		$screen = Screen::find($screenId);

		if (!$screen) {
			return response('Not found', 404);
		}

		$cacheKey = $this->getCacheKey($screenId);

		if (Cache::tags(self::CACHE_TAGS)->has($cacheKey)) {
			$slides = Cache::tags(self::CACHE_TAGS)->get($cacheKey);
		} else {
			$slides = $this->fetchSlides($screen);
			if (!empty($slides)) {
				Cache::tags(self::CACHE_TAGS)->forever($cacheKey, $slides);
			}
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

	private function getCacheKey($screenId) {
		return sprintf('%s-%d-%d', self::CACHE_KEY_PREFIX, $screenId, self::CACHE_VERSION);
	}
}
