<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UpdateAnnotation;
use App\Models\Annotation;
use App\Models\Keyword;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cache;

class AnnotationsApiController extends ApiController
{
	const AVAILABLE_FILTERS = [
		'search'
	];

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

		$keywords = array_map(function($keyword) {
			return new Keyword(['text' => $keyword]);
		}, $request->keywords);

		$annotation->keywords()->delete();
		$annotation->keywords()->saveMany($keywords);

		Cache::tags($this->resourceName)->flush();

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

		$keywords = array_map(function($keyword) {
			return new Keyword(['text' => $keyword]);
		}, $request->keywords);

		$annotation->tags()->sync($tagIds);
		$annotation->keywords()->delete();
		$annotation->keywords()->saveMany($keywords);

		$annotation->save();
		$annotation->searchable();

		Cache::tags($this->resourceName)->flush();

		return $this->transformAndRespond($annotation);
	}
}
