<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Log;

class QuestionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.questions');
	}

	public function post(Request $request)
	{
		Log::debug($request->input->all());
	}
}
