<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UpdateAnnotation;
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

		// TODO check if user can add

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

	public function put(UpdateAnnotation $request, $annotationId) {
		$annotation = Annotation::find($annotationId);

		if (empty($annotation)) {
			return $this->respondNotFound();
		}

		$annotation->keyword =  $request->input('keyword') ?? $annotation->keyword;
		$annotation->description =  $request->input('description') ?? $annotation->description;

		$annotation->tags()->sync($request->tags);

		$annotation->save();

		return $this->transformAndRespond($annotation);
	}
}
