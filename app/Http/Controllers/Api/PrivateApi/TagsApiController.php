<?php

namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.tags');
	}

	public function getAll(Request $request) {
		$tags = Tag::all();

		return $this->json($tags);
	}
}
