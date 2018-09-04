<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;
use App\Models\Category;
use App\Models\Screen;
use App\Models\Slide;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SlideshowBuilderApiController extends ApiController
{
	use TranslatesApiQueries;

	const CACHE_VERSION = '1';
	const CACHE_KEY_PATTERN = 'slideshow-builder-%s-%s';
	const CACHE_TTL = 60 * 24 * 7;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.slides');
	}

	public function getEmpty()
	{
		$view = view('course.slideshow', [
			'slides'         => '',
			'background_url' => '',
		]);

		$view->render();

		return response($view);
	}

	public function get($slideshowId)
	{
		$key = $this->key($slideshowId);
		if (Cache::has($key)) {
			return $this->respondFromCache($key);
		}

		$slideshow = Slideshow::find($slideshowId);

		if (!$slideshow) {
			return response('Not found', 404);
		}

		$slides = $slideshow
			->slides()
			->orderBy('order_number')
			->get();

		return $this->renderView($slides, $slideshow->background_url, $key);
	}

	public function preview(Request $request)
	{
		$screenId = $request->get('screenId');
		$slideId = $request->get('slideId');
		$content = $request->get('content');

		if (empty($screenId) && empty($slideId)) {
			return $this->respondInvalidInput('Pass either screenId or slideId');
		}

		if (!empty($screenId)) {
			$screen = Screen::find($screenId);
		} else {
			$slide = Slide::find($slideId);

			if (!$slide) {
				return response('slide not found', 404);
			}

			if (!empty($slide->sections) && !empty($slide->sections->first())) {
				$section = $slide->sections->first();
				$screen = $section->screen;
			}
		}

		if (!$screen) {
			return response('screen not found', 404);
		}

		if (!empty($screen->slideshow)) {
			$backgroundUrl = $screen->slideshow->background_url;
		} else {
			$backgroundUrl = '';
		}

		$view = view('course.slideshow', [
			'slides'         => $content,
			'background_url' => $backgroundUrl,
		]);

		$view->render();

		return response($view);
	}

	public function byCategory($categoryId)
	{
		$key = $this->key($categoryId);
		if (Cache::has($key)) {
			return $this->respondFromCache($key);
		}

		$category = Category::find($categoryId);

		if (!$category) {
			return $this->respondNotFound('Category not found');
		}

		$slides = $category
			->slides()
			->orderBy('order_number')
			->get();

		$screen = Screen::select()
			->whereHas('tags', function ($query) use ($category) {
				$query->where('tags.name', $category->name);
			})
			->where('type', 'slideshow')
			->first();

		$background = $screen->slideshow->background_url ?? '';

		return $this->renderView($slides, $background, $key);
	}

	public function query(Request $request)
	{
		$key = $this->key(md5(json_encode($request->all())));
		if (Cache::has($key)) {
			return $this->respondFromCache($key);
		}

		$builder = $this->applyFilters(new Slide, $request);
		$slides = $builder->get();
		if (!$slides->first()) {
			return $this->respondNotFound();
		}
		$firstSlide = Slide::find($slides->first()->slide_id);
		$background = $firstSlide->slideshow->first()->background_url;

		return $this->renderView($slides, $background, $key);
	}

	protected function renderView($slides, $background, $cacheKey = false)
	{
		$search = [
			'<p>&nbsp;</p>',
			'data-has-custom-html=""',
			'style="z-index: 11;"',
			'data-block-type="text"',
			'font-family: Lato;',
			'font-variant-ligatures: normal;',
			'font-variant-caps: normal;',
			'font-variant-caps: normal;',
			'font-weight: normal;',
			'data-has-line-height=""',
			'font-style: normal;',
			'font-family: &quot;Open Sans&quot;, sans-serif;',
			'data-placeholder-text="Text"',
			'data-placeholder-tag="p"',
			'   ',
			"\n",
		];

		$replace = ['<br>',''];

		$slidesContent = str_replace($search, $replace, $slides->implode('content', ' '));
		$viewData = [
			'slides' => $slidesContent,
			'background_url' => $background,
		];

		if ($cacheKey) Cache::put($cacheKey, $viewData, self::CACHE_TTL);
		
		$view = view('course.slideshow', $viewData);

		return response($view->render());
	}

	private function respondFromCache($key) {
		$view = view('course.slideshow', Cache::get($key));

		return response($view->render());
	}

	private function key($identifier) {
		return sprintf(self::CACHE_KEY_PATTERN, self::CACHE_VERSION, $identifier);
	}
}
