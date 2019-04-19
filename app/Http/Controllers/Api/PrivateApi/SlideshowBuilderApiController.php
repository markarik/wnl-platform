<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Category;
use App\Models\Screen;
use App\Models\Slide;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SlideshowBuilderApiController extends ApiController
{
	const CACHE_VERSION = '1';
	const CACHE_KEY_PATTERN = 'slideshow_builder-%s-%s';
	const SLIDESHOW_SUBKEY = 'slideshow:%s';
	const CATEGORY_SUBKEY = 'category:%s';
	const SLIDE_SUBKEY = 'slide:%s';
	const CACHE_TTL = 60 * 24 * 7;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.slides');
	}

	public function getEmpty()
	{
		$view = view('course.slideshow', [
			'slides' => '',
			'background_url' => '',
		]);

		$view->render();

		return response($view);
	}

	public function getHtml($slideshowId)
	{
		$key = self::key(sprintf(self::SLIDESHOW_SUBKEY, $slideshowId));
		if (Cache::has($key)) {
			return $this->respond(Cache::get($key));
		}

		$slideshow = Slideshow::find($slideshowId);

		if (!$slideshow) {
			return response('Not found', 404);
		}

		$slides = $slideshow
			->slides()
			->get();

		$viewData = $this->getViewData($slides, $slideshow->background_url);
		Cache::put($key, $viewData, self::CACHE_TTL);

		return $this->respond($viewData);
	}

	public function preview(Request $request)
	{
		$screenId = $request->get('screenId');
		$slideId = $request->get('slideId');
		$content = $request->get('content');
		$screen = null;

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
			'slides' => $content,
			'background_url' => $backgroundUrl,
		]);

		$view->render();

		return response($view);
	}

	public function byCategory($categoryId)
	{
		$key = self::key(sprintf(self::CATEGORY_SUBKEY, $categoryId));
		if (Cache::has($key)) {
			return $this->respond(Cache::get($key));
		}

		$category = Category::find($categoryId);

		if (!$category) {
			return $this->respondNotFound('Category not found');
		}

		$slides = $category
			->slides()
			->get();

		$screen = Screen::select()
			->whereHas('tags', function ($query) use ($category) {
				$query->where('tags.name', $category->name);
			})
			->where('type', 'slideshow')
			->first();

		$background = $screen->slideshow->background_url ?? '';

		$viewData = $this->getViewData($slides, $background);
		Cache::put($key, $viewData, self::CACHE_TTL);

		return $this->respond($viewData);
	}

	public function bySlideId(Request $request)
	{
		$slideId = $request->route('slideId');
		$key = self::key(sprintf(self::SLIDE_SUBKEY, $slideId));
		if (Cache::has($key)) {
			return $this->respond(Cache::get($key));
		}

		$slides = Slide::where('id', $slideId)->get();

		$background = $slides->first()->slideshow->first()->background_url;

		$viewData = $this->getViewData($slides, $background);
		Cache::put($key, $viewData, self::CACHE_TTL);

		return $this->respond($viewData);
	}

	public function byCategorySlides(Request $request)
	{
		$categoryId = $request->route('categoryId');
		$slidesIds = $request->get('slidesIds');
		$key = self::key(md5(json_encode($request->all())));

		$slides = Slide::select('slides.*')
			->where('presentables.presentable_type', 'App\\Models\\Category')
			->where('presentables.presentable_id', $categoryId)
			->whereIn('slides.id', $slidesIds)
			->join('presentables', 'slides.id', '=', 'presentables.slide_id')
			->orderBy('presentables.order_number', 'asc')
			->get();

		if (!$slides->count()) {
			return $this->respondNotFound();
		}

		$background = $slides->first()->slideshow->first()->background_url ?? null;

		$viewData = $this->getViewData($slides, $background);
		Cache::tags("slideshow_builder-category-{$categoryId}")->put($key, $viewData, self::CACHE_TTL);

		return $this->respond($viewData);
	}

	protected function getViewData($slides, $background)
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
			'font-style: normal;',
			'font-family: &quot;Open Sans&quot;, sans-serif;',
			'data-placeholder-text="Text"',
			'data-placeholder-tag="p"',
			'   ',
			"\n",
		];

		$replace = ['<br>', ''];

		$slidesContent = str_replace($search, $replace, $slides->implode('content', ' '));

		// Force lazy loading
		$slidesContent = preg_replace('/\ssrc="(.+)"/mU', ' data-src="$1"', $slidesContent);

		return [
			'slides' => $slidesContent,
			'background_url' => $background,
		];
	}

	private function respond($viewData)
	{
		$view = view('course.slideshow', $viewData);

		return response($view->render());
	}

	public static function key($identifier)
	{
		return sprintf(self::CACHE_KEY_PATTERN, self::CACHE_VERSION, $identifier);
	}
}
