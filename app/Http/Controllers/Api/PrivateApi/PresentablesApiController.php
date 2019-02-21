<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Http\Controllers\Api\ApiController;
use App\Models\Presentable;
use Illuminate\Http\Request;

class PresentablesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.presentables');
	}

	public function getSlideByOrderNumber(Request $request) {
		$presentableType = $request->get('presentable_type');
		$presentableId = $request->get('presentable_id');
		$orderNumber = $request->get('order_number');

		$presentable = Presentable::where('presentable_type', $presentableType)
			->where('presentable_id', $presentableId)
			->where('order_number', $orderNumber)
			->get();

		if ($presentable->count() === 0) {
			return $this->respondNotFound();
		}

		return $this->transformAndRespond($presentable);
	}

	public function getSlides(Request $request) {
		$presentableType = $request->get('presentable_type');
		$presentableId = $request->get('presentable_id');

		$presentables = Presentable::where('presentable_type', $presentableType)
			->where('presentable_id', $presentableId)
			->join('slides', 'presentables.slide_id', '=', 'slides.id')
			->get();

		return $this->transformAndRespond($presentables);
	}
}
