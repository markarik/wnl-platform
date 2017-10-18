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

		$slides = Slide::select()
			->whereHas('tags', function ($query) use ($category) {
				$query->where('tags.name', $category->name);
			})
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

	protected function renderView($slides, $background)
	{
		$view = view('course.slideshow', [
			'slides'         => $slides->implode('content', ' '),
			'background_url' => $background,
		]);

		$view->render();

		return response($view);
	}
}
