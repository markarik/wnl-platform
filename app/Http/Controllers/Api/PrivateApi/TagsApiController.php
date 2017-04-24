<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class TagsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.tags');
	}
}
