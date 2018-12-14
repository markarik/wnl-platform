<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateTag;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagsApiController extends ApiController {
	public function __construct(Request $request) {
		parent::__construct($request);
		$this->resourceName = config('papi.resources.tags');
	}

	public function byName(Request $request) {
		$request->validate([
			'excludedIds' => 'array',
			'name' => 'string',
		]);

		$matchingTagsQuery = Tag::where('name', 'like', "%{$request->get('name')}%");

		if ($request->has('excludedIds')) {
			$matchingTagsQuery->whereNotIn('id', $request->get('excludedIds'));
		}

		$matchingTags = $matchingTagsQuery->get();

		return $this->transformAndRespond($matchingTags);
	}

	public function post(UpdateTag $request) {
		$tag = Tag::create($request->all());

		return $this->transformAndRespond($tag);
	}

	public function put(UpdateTag $request) {
		$tag = Tag::find($request->route('id'));

		if (!$tag) {
			return $this->respondNotFound();
		}

		$tag->update($request->all());

		return $this->respondOk();
	}

	public function delete($id) {
		$tag = Tag::find($id);

		if (!$tag) {
			return $this->respondNotFound();
		}

		if ($tag->isProtected()) {
			return $this->respondUnprocessableEntity(['message' => 'This tag is protected, you can\' delete it']);
		}

		if ($tag->hasRelations() || $tag->isInTaxonomy()) {
			// TODO
			return $this->respondNotImplemented();
		}

		$tag->delete();

		return $this->respondOk();
	}
}
