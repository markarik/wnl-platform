<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CoursesApiController extends Controller
{
	/**
	 * @return string/json
	 */
	public function getNavigation($courseId)
	{
		$course = Course::find($courseId);

		if (!$course) {
			return response('Course not found', 404);
		}

		$breadcrumbs = [
			[
				'type' => 'course',
				'icon' => 'course',
				'id'  => $course->id,
				'text' => $course->name,
			],
		];
		$items = [];

		$groups = $course->groups()->with('lessons')->get();

		foreach ($groups as $group) {
			$items[] = [
				'type'     => 'group',
				'icon'     => 'group',
				'url'      => '#',
				'text'     => $group->name,
			];

			foreach ($group->lessons as $lesson){
				$items[] = [
					'courseId' => $course->id,
					'type'     => 'lesson',
					'icon'     => 'lesson',
					'id'       => $lesson->id,
					'text'     => $lesson->name,
				];
			}
		}

		return response()->json(['breadcrumbs' => $breadcrumbs, 'items' => $items]);
	}
}
