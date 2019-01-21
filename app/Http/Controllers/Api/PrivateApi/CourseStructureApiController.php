<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\CourseStructureNodeTransformer;
use App\Models\CourseStructureNode;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;

class CourseStructureApiController extends ApiController
{

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.course-structure');
	}

	public function getStructure($courseId)
	{
		$nodes = CourseStructureNode::where('course_id', $courseId)->get();

		$data = $this->transformTree($nodes->toTree());

		return $this->respondOk($data);
	}

	private function transformTree($tree)
	{
		$includes = [];
		$traverse = function ($nodes) use (&$traverse, &$includes) {

			$resource = new Collection($nodes, new CourseStructureNodeTransformer, 'course_structure_nodes');
			$transformedNodes = $this->fractal->createData($resource)->toArray();

			$includes = array_merge($includes, $transformedNodes['included']);
			unset($transformedNodes['included']);

			foreach ($transformedNodes as $key => $transformedNode) {
				if(count($transformedNode['children'])){
					$transformedNodes[$key]['children'] = $traverse($transformedNode['children']);
				}
			}

			return $transformedNodes;
		};

		$transformedTree = $traverse($tree);
		return array_merge($transformedTree, ['included' => $includes]);
	}
}
