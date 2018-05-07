<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Annotation;
use Illuminate\Http\Request;

class AnnotationsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.annotations');
	}

	public function post(Request $request) {
		$annotation = new Annotation();

		$annotation->keyword = $request->keyword;
		$annotation->description = $request->description;
		$annotation->save();

		$transformed = $this->transform($annotation);
		return $this->respondOk($transformed);
	}
}
