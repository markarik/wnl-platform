<?php

namespace App\Http\Controllers\Course;

use App\Models\Snippet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SlideShowController extends Controller
{

	const CACHE_KEY_PREFIX = 'snippet-slideshow-';

	const CACHE_TAGS = ['slideshows', 'snippet-slideshows'];

	public function build($snippetId)
	{
		$snippet = Snippet::find($snippetId);

		if (!$snippet) {
			return response('Not found', 404);
		}

		$cacheKey = self::CACHE_KEY_PREFIX . $snippetId;

		if (Cache::tags(self::CACHE_TAGS)->has($cacheKey)) {
			$slides = Cache::tags(self::CACHE_TAGS)->get($cacheKey);
		} else {
			$slides = $this->fetchSlides($snippet);
			Cache::tags(self::CACHE_TAGS)->forever($cacheKey, $slides);
		}

		$view = view('course.slide-show', ['slides' => $slides]);
		$view->render();

		return response($view);
	}

	/**
	 * @param $snippet
	 * @return string
	 */
	public function fetchSlides($snippet)
	{

		$slides = $snippet->slides;

		return $slides;
	}
}
