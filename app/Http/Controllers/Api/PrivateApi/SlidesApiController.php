<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Concerns\Slides\AddsSlides;
use App\Http\Controllers\Api\Concerns\Slides\RemovesSlides;
use App\Http\Controllers\Api\Transformers\SlideTransformer;
use App\Http\Requests\Course\DetachSlide;
use App\Http\Requests\Course\PostSlide;
use App\Http\Requests\Course\UpdateSlide;
use App\Http\Requests\Course\UpdateSlideChart;
use App\Jobs\SearchImportAll;
use App\Models\Screen;
use App\Models\Slide;
use Artisan;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use Lib\SlideParser\Parser;

class SlidesApiController extends ApiController
{
	use AddsSlides, RemovesSlides;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.slides');
	}

	public function put(UpdateSlide $request)
	{
		$slide = Slide::find($request->route('id'));

		if (!$slide) {
			return $this->respondNotFound();
		}

		$content = $request->get('content');
		$isFunctional = $request->get('is_functional');

		$parser = new Parser;
		$content = $parser->handleCharts($content);
		$content = $parser->handleImages($content);

		$slide->update([
			'content'       => $content,
			'is_functional' => $isFunctional,
		]);

		$resource = new Item($slide, new SlideTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	/**
	 * @param PostSlide $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function post(PostSlide $request)
	{
		$screen = Screen::find($request->screen);
		$content = $request->get('content');
		$slideshow = $screen->slideshow;
		$orderNumber = $request->order_number - 1; // https://goo.gl/ZzMWT3

		$currentSlide = $this->getCurrentFromPresentables($slideshow->id, $orderNumber);
		$presentables = $this->getSlidePresentables($currentSlide, $screen);

		$presentables->push($slideshow);
		$presentables = $this->getPresentablesOrder($currentSlide, $presentables);

		$this->incrementOrderNumber($presentables);

		$parser = new Parser;
		$content = $parser->handleCharts($content);
		$content = $parser->handleImages($content);

		$slide = Slide::create([
			'is_functional' => empty($request->is_functional) ? false : true,
			'content'       => $content,
		]);

		$this->attachSlide($slide, $presentables);

		dispatch(new SearchImportAll('App\\Models\\Slide'));
		\Artisan::queue('screens:countSlides');
		\Artisan::call('cache:tag', ['tag' => 'presentables,slides']);

		return $this->respondOk();
	}

	public function updateCharts(UpdateSlideChart $request)
	{
		$id = $request->route('slideId');
		$slide = Slide::find($id);
		if (!$slide) {
			return $this->respondNotFound();
		}

		Artisan::call('charts:update', ['id' => $id]);

		$resource = new Item($slide->fresh(), new SlideTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function detach(DetachSlide $request)
	{
		$slide = Slide::find($request->get('slideId'));
		$screen = Screen::find($request->get('screenId'));
		$slideshow = $screen->slideshow ?? null;
		if (!$slide || !$screen || !$slideshow) {
			return $this->respondInvalidInput();
		}

		$orderNumber = $this->getSlideOrderNumber($slide, $slideshow);
		$currentSlide = $this->getCurrentFromPresentables($slideshow->id, $orderNumber);
		$presentables = $this->getSlidePresentables($currentSlide, $screen);

		$presentables->push($slideshow);
		$presentables = $this->getPresentablesOrder($currentSlide, $presentables);

		$this->decrementOrderNumber($presentables);
		$this->detachSlide($slide, $presentables);

		dispatch(new SearchImportAll('App\\Models\\Slide'));
		\Artisan::queue('screens:countSlides');
		\Artisan::call('cache:tag', ['tag' => 'presentables,slides']);

		return $this->respondOk();
	}
}
