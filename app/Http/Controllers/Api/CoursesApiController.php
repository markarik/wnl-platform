<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

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

		$resources = Config::get('papi.resources');

		$breadcrumbs = [
			[
				'type' => $resources['courses'],
				'id' => $course->id,
				'name' => $course->name,
				'ancestors' => [],
			],
		];
		$items = [];

		$groups = $course->groups()->with('lessons')->get();

		foreach ($groups as $group) {
			$items[] = [
				'type'      => $resources['groups'],
				'id'        => $group->id,
				'name'      => $group->name,
				'ancestors' => [
					$resources['courses'] => $course->id,
				],
			];

			foreach ($group->lessons as $lesson){
				$items[] = [
					'type' => $resources['lessons'],
					'id' => $lesson->id,
					'name' => $lesson->name,
					'ancestors' => [
						$resources['courses'] => $course->id,
						$resources['groups'] => $group->id,
					],
				];
			}
		}

		return response()->json(['breadcrumbs' => $breadcrumbs, 'items' => $items]);
	}
}
