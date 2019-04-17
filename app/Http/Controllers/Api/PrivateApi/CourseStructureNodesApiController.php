<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Concerns\OperatesOnNestedSets;
use App\Http\Requests\Course\MoveCourseStructureNode;
use App\Http\Requests\Course\UpdateCourseStructureNode;
use App\Models\CourseStructureNode;
use App\Models\Lesson;
use Auth;
use Illuminate\Http\Request;
use Kalnoy\Nestedset\Collection;
use Kalnoy\Nestedset\QueryBuilder;

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
		$model = $this->eagerLoadIncludes(CourseStructureNode::class);
		/** @var QueryBuilder $queryBuilder */
		$queryBuilder = $model->where('course_id', $courseId);

		/** @var Collection $nodes */
		$nodes = $queryBuilder->defaultOrder()->get();
		$nodes = $nodes->toFlatTree();

		if (!Auth::user()->isAdmin()) {
			$nodes = $this->getNodesWithoutNotAccessibleLessons($nodes);
		}

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

	private function getNodesWithoutNotAccessibleLessons(Collection $nodes): Collection
	{
		$nodes = $nodes->filter(function ($node) {
			/** @var CourseStructureNode $node */
			if ($node->structurable_type === Lesson::class) {
				/** @var Lesson $lesson */
				$lesson = $node->structurable;

				return $lesson->isAccessible();
			}

			return true;
		});

		return $nodes;
	}
}
