<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Annotation;
use App\Models\Tag;
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

		if ($request->has('tags')) {
			foreach ($request->tags as $tag) {
				$tagModel = Tag::firstOrCreate(['id' => $tag['id']]);

				$annotation->tags()->attach($tagModel);
			}
		}

		$transformed = $this->transform($annotation);
		return $this->respondOk($transformed);
	}
}
