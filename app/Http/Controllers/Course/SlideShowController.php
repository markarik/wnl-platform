<?php

namespace App\Http\Controllers\Course;

use App\Models\Screen;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SlideShowController extends Controller
{

	const CACHE_KEY_PREFIX = 'screens-slideshows';
	const CACHE_VERSION = 2;
	const CACHE_TAGS = ['slideshows', 'screens-slideshows', 'slides'];

	public function build($slideshowId)
	{
		$slideshow = Slideshow::find($slideshowId);

		if (!$slideshow) {
			return response('Not found', 404);
		}

		$cacheKey = $this->getCacheKey($slideshowId);

		if (Cache::tags(self::CACHE_TAGS)->has($cacheKey)) {
			$slides = Cache::tags(self::CACHE_TAGS)->get($cacheKey);
		} else {
			$slides = $this->fetchSlides($slideshow);
			if (!empty($slides)) {
				Cache::tags(self::CACHE_TAGS)->forever($cacheKey, $slides);
			}
		}

		$view = view('course.slideshow', [
			'slides' => $slides,
			'background_url' => $slideshow->background_url,
		]);
		$view->render();

		return response($view);
	}

	/**
	 * @param $slideshow
	 * @return string
	 */
	public function fetchSlides($slideshow)
	{
		$slides = $slideshow->slides;
		return $slides;
	}

	private function getCacheKey($slideshowId)
	{
		return sprintf('%s-%d-%d', self::CACHE_KEY_PREFIX, $slideshowId, self::CACHE_VERSION);
	}
}
