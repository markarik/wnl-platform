<?php

namespace App\Http\Controllers\Api\PrivateApi\Course;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class CoursesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.courses');
	}
}
