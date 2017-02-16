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
				'url'  => route('course', $course->id),
				'text' => $course->name,
			],
		];
		$navigation = [];

		$groups = $course->groups()->with('lessons')->get();

		foreach ($groups as $group) {
			$navigation[] = [
				'type'     => 'group',
				'icon'     => 'group',
				'url'      => '#',
				'text'     => $group->name,
				'isActive' => 'bool dupy',
				'state'    => 'California',
			];

			foreach ($group->lessons as $lesson){
				$navigation[] = [
					'type'     => 'lesson',
					'icon'     => 'lesson',
					'url'      => route('lesson', [$course->id, $lesson->id]),
					'text'     => $lesson->name,
					'isActive' => 'bool dupy',
					'state'    => 'California',
				];
			}
		}

//		return view('layouts.guest');

		return response()->json(['breadcrumbs' => $breadcrumbs, 'navigation' => $navigation]);
	}
}
