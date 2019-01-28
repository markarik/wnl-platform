<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\CourseStructureNodeTransformer;
use App\Http\Requests\Course\MoveCourseStructureNode;
use App\Http\Requests\Course\UpdateCourseStructureNode;
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
		$nodes = CourseStructureNode::where('course_id', $courseId)->defaultOrder()->get()->toFlatTree();

		return $this->transformAndRespond($nodes);
	}

	public function post(UpdateCourseStructureNode $request) {
		$parentTaxonomyTerm = null;
		if ($request->parent_id) {
			$parentTaxonomyTerm = CourseStructureNode::find($request->parent_id);
		}

		$taxonomyTerm = CourseStructureNode::create($request->all(), $parentTaxonomyTerm);

		return $this->transformAndRespond($taxonomyTerm);
	}

	public function put(UpdateCourseStructureNode $request) {
		$taxonomyTerm = CourseStructureNode::find($request->route('id'));
		$newParentId = $request->get('parent_id');

		if ($newParentId !== $taxonomyTerm->parent_id) {
			if ($newParentId === null) {
				$taxonomyTerm->makeRoot();
			} else {
				$taxonomyTerm->appendToNode(CourseStructureNode::find($newParentId));
			}
		}

		if (!$taxonomyTerm) {
			return $this->respondNotFound();
		}

		$taxonomyTerm->update($request->all());

		return $this->transformAndRespond($taxonomyTerm);
	}

	public function move(MoveCourseStructureNode $request) {
		$target = CourseStructureNode::find($request->get('id'));
		$direction = $request->get('direction');

		if ($direction === 0) {
			return $this->respondOk();
		}

		if ($direction > 0) {
			$success = $target->down($direction);
		} else {
			$success = $target->up(abs($direction));
		}

		if (!$success) {
			return $this->respondUnprocessableEntity('direction out of range');
		}

		return $this->respondOk();
	}
}
