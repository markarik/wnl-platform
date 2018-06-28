<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Concerns\TranslatesApiQueries;
use App\Models\Category;
use App\Models\Screen;
use App\Models\Slide;
use App\Models\Slideshow;
use Illuminate\Http\Request;

class SlideshowBuilderApiController extends ApiController
{
	use TranslatesApiQueries;

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
		$slideshow = Slideshow::find($slideshowId);

		if (!$slideshow) {
			return response('Not found', 404);
		}

		$slides = $slideshow
			->slides()
			->orderBy('order_number')
			->get();

		return $this->renderView($slides, $slideshow->background_url);
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

		return $this->renderView($slides, $background);
	}

	public function query(Request $request)
	{
		$builder = $this->applyFilters(new Slide, $request);
		$slides = $builder->get();
		if (!$slides->first()) {
			return $this->respondNotFound();
		}
		$firstSlide = Slide::find($slides->first()->slide_id);
		$background = $firstSlide->slideshow->first()->background_url;

		return $this->renderView($slides, $background);
	}

	protected function renderView($slides, $background)
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

		$replace = [
			'<br>',
			'',
		];

		$view = view('course.slideshow', [
			'slides'         => str_replace($search, $replace, $slides->implode('content', ' ')),
			'background_url' => $background,
		]);

		$view->render();

		return response($view);
	}
}
