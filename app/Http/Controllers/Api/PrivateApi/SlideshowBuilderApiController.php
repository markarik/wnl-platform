<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Models\Category;
use App\Models\Screen;
use App\Models\Slide;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class SlideshowBuilderApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.slides');
	}

	public function get($slideshowId)
	{
		$slideshow = Slideshow::find($slideshowId);

		if (!$slideshow) {
			return response('Not found', 404);
		}

		return $this->renderView($slideshow->slides, $slideshow->background_url);
	}

	public function byCategory($categoryId)
	{
		$category = Category::find($categoryId);

		if (!$category) {
			return $this->respondNotFound('Category not found');
		}

		$slides = Slide::select()
			->whereHas('tags', function ($query) use ($category) {
				$query->where('tags.name', $category->name);
			})
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
			''
		];

		$view = view('course.slideshow', [
			'slides'         => str_replace($search, $replace, $slides->implode('content', ' ')),
			'background_url' => $background,
		]);

		$view->render();

		return response($view);
	}
}
