<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;

class SnippetApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.snippets');
	}
}
