<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UpdateAnnotation;
use App\Models\Annotation;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnotationsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.annotations');
	}

	public function post(Request $request) {
		$annotation = new Annotation();

		if (!Auth::user()->isAdmin()) {
			return $this->respondForbidden();
		}

		$annotation->title = $request->title;
		$annotation->description = $request->description;
		$annotation->save();

		if ($request->has('tags')) {
			foreach ($request->tags as $tag) {
				$tagModel = Tag::firstOrCreate(['id' => $tag['id']]);

				$annotation->tags()->attach($tagModel);
			}
		}

		return $this->transformAndRespond($annotation);
	}

	public function put(UpdateAnnotation $request, $annotationId) {
		$annotation = Annotation::find($annotationId);

		if (empty($annotation)) {
			return $this->respondNotFound();
		}

		$annotation->title =  $request->input('title') ?? $annotation->title;
		$annotation->description =  $request->input('description') ?? $annotation->description;
		$tagIds = array_map(function($tag) {
			return $tag['id'];
		}, $request->tags);

		$annotation->tags()->sync($tagIds);

		$annotation->save();

		return $this->transformAndRespond($annotation);
	}
}
