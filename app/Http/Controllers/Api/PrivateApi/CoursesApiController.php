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

	public function getStructure($id)
	{
		$data = $this->get($id)->getData();
		return $this->respondOk($data);
	}

	public function put(UpdateCourse $request, $id) {
		$course = Course::find($id);

		if (empty($course)) {
			return $this->respondNotFound();
		}

		DB::transaction(function() use ($course, $request) {
			$course->update($request->all());

			foreach ($course->groups as $group) {
				$group->order_number = array_search($group->id, $request->groups) + 1;
				$group->save();
			}
		});

		return $this->transformAndRespond($course);
	}
}
