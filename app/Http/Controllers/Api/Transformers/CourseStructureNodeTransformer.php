<?php


namespace App\Http\Controllers\Api\Transformers;


use App\Http\Controllers\Api\ApiTransformer;
use App\Models\CourseStructureNode;
use App\Models\Lesson;
use League\Fractal\Resource\Collection;

class CourseStructureNodeTransformer extends ApiTransformer
{
	protected $availableIncludes = ['lessons', 'groups'];

	public function transform(CourseStructureNode $node)
	{
		$data = [
			'id'                => $node->id,
			'structurable_type' => $node->structurable_type,
			'structurable_id'   => $node->structurable_id,
			'order_number'      => $node->order_number,
			'children'          => $node->children,
		];

		return $data;
	}

	public function includeLessons(CourseStructureNode $node)
	{
		if ($node->structurable_type === 'App\\Models\\Lesson') {
			$lesson = $node->structurable;

			return $this->item($lesson, new LessonTransformer(['course_structure_nodes' => $node->id]), 'lessons');
		}
	}

	public function includeGroups(CourseStructureNode $node)
	{
		if ($node->structurable_type === 'App\\Models\\Group') {
			$group = $node->structurable;

			return $this->item($group, new GroupTransformer(['course_structure_nodes' => $node->id]), 'groups');
		}
	}
}
