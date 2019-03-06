<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateCourse;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoursesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.courses');
	}

	public function put(UpdateCourse $request, $id) {
		$course = Course::find($id);

		if (empty($course)) {
			return $this->respondNotFound();
		}

		$course->update($request->all());

		return $this->transformAndRespond($course);
	}
}
