<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ComposeSidenavItems;
use Illuminate\Support\Facades\Config;

class CoursesApiController extends Controller
{
	use ComposeSidenavItems;

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
			$this->composeItem($resources['courses'], $course->id, $course->name),
		];
		$items = [];

		$groups = $course->groups()->with('lessons')->get();

		foreach ($groups as $group) {
			$items[] = $this->composeItem($resources['groups'], $group->id,
				$group->name, [$resources['courses'] => $course->id]);

			foreach ($group->lessons as $lesson){
				$items[] = $this->composeItem(
					$resources['lessons'],
					$lesson->id,
					$lesson->name,
					[
						$resources['courses'] => $course->id,
						$resources['groups'] => $group->id,
					]
				);
			}
		}

		return response()->json(['breadcrumbs' => $breadcrumbs, 'items' => $items]);
	}
}
