<?php


namespace App\Http\Controllers\Api\PrivateApi\Course;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateSlide;
use App\Models\Slide;
use Illuminate\Http\Request;

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
}
