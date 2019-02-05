<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Concerns\OperatesOnNestedSets;
use App\Http\Requests\Course\MoveCourseStructureNode;
use App\Http\Requests\Course\UpdateCourseStructureNode;
use App\Models\CourseStructureNode;
use Illuminate\Http\Request;

class CourseStructureNodesApiController extends ApiController
{

	use OperatesOnNestedSets;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.course-structure-nodes');
	}


	public function getByCourseId($courseId)
	{
		$nodes = CourseStructureNode::where('course_id', $courseId)->defaultOrder()->get()->toFlatTree();

		return $this->transformAndRespond($nodes);
	}

	public function post(UpdateCourseStructureNode $request)
	{
		return $this->postNode($request);
	}

	public function put(UpdateCourseStructureNode $request)
	{
		return $this->putNode($request);
	}

	public function move(MoveCourseStructureNode $request)
	{
		return $this->moveNode($request);
	}
}
