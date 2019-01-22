<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\CourseStructureNodeTransformer;
use App\Models\CourseStructureNode;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;

class CourseStructureNodesApiController extends ApiController
{

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.course-structure-nodes');
	}

	public function getByCourseId($courseId)
	{
		$nodes = CourseStructureNode::where('course_id', $courseId)->get()->toFlatTree();

		return $this->transformAndRespond($nodes);
	}
}
