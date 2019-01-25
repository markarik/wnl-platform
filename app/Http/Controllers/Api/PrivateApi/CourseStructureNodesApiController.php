<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\CourseStructureNode;
use Illuminate\Http\Request;

class CourseStructureNodesApiController extends ApiController
{

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
}
