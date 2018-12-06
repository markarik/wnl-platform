<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.tags');
	}

	public function byName(Request $request) {
		$matchingTags = Tag::whereNotIn('id', $request->get('excludedIds'))
			->where('name', 'like', "%{$request->get('name')}%")
			->get();

		return $this->transformAndRespond($matchingTags);
	}
}
