<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Concerns\Slides\AddsSlides;
use App\Http\Requests\Course\PostSlide;
use App\Http\Requests\Course\UpdateSlide;
use App\Jobs\SearchImportAll;
use App\Models\Screen;
use App\Models\Slide;
use Lib\SlideParser\Parser;
use Illuminate\Http\Request;
use Artisan;
use DB;

class SlidesApiController extends ApiController
{
	use AddsSlides;

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

		$slide->update($request->all());

		return $this->respondOk();
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

		// Get slide that currently has the given order no. + get its section, subsection etc.
		$currentSlide = $this->getCurrentFromPresentables($slideshow->id, $orderNumber);
		list ($section, $subsection, $categories) = $this->getSlidePresentables($currentSlide, $screen);

		// Incr. order no. of all slides above the submitted order no.
		$this->incrementOrderNumber($orderNumber, $slideshow, $section, $subsection, $categories);

		// Handle chart and images
		$parser = new Parser;
		$content = $parser->handleCharts($content);
		$content = $parser->handleImages($content);

		// Create new slide
		$slide = Slide::create([
			'is_functional' => empty($request->is_functional) ? false : true,
			'content'       => $content,
		]);

		// Attach slide to screen, section, subsection etc.
		$this->attachSlide($slide, $orderNumber, $slideshow, $section, $subsection, $categories);

		// Re-index all slides... Psayayay
		dispatch(new SearchImportAll('App\\Models\\Slide'));

		return $this->respondOk();
	}
}
