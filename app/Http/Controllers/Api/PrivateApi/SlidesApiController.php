<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\PostSlide;
use App\Http\Requests\Course\UpdateSlide;
use App\Models\Presentable;
use App\Models\Screen;
use App\Models\Slide;
use Illuminate\Http\Request;
use DB;

class SlidesApiController extends ApiController
{
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
		$slideshow = $screen->slideshow;
		$orderNumber = $request->order_number;

		// get slide that currently has the given order no.
		// + get its section and subsection
		$currentSlideId = Presentable::select(['slide_id'])
			->where('presentable_id', $slideshow->id)
			->where('presentable_type', 'App\\Models\\Slideshow')
			->where('order_number', $orderNumber)
			->first()
			->slide_id;
		$currentSlide = Slide::find($currentSlideId);

		$section = $currentSlide->sections()
			->whereHas('screen', function ($query) use ($screen) {
				$query->where('id', $screen->id);
			})->first();

		// Incr. order no. of all slides above the submitted order no.
		DB::statement(implode(' ', [
			"update presentables set order_number = (order_number + 1)",
			"where order_number >= {$orderNumber}",
			"and ((presentable_type = 'App\\\\Models\\\\Slideshow' and presentable_id = {$slideshow->id}) or",
			"(presentable_type = 'App\\\\Models\\\\Section' and presentable_id = {$section->id}))",
		]));

		// Create new slide
		$slide = Slide::create([
			'is_functional' => $request->is_functional,
			'content'       => $request->content,
		]);

		// Attach slide to screen, section, subsection etc.
		$slideshow->slides()->attach($slide, ['order_number' => $orderNumber]);
		$section->slides()->attach($slide, ['order_number' => $orderNumber]);

		return $this->respondOk();
	}
}
