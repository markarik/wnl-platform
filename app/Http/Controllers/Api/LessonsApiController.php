<?php

namespace App\Http\Controllers\Api;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class LessonsApiController extends Controller
{
	/**
	 * @return string/json
	 */
	public function getNavigation($lessonId)
	{
		$lesson = Lesson::find($lessonId);

		if (!$lesson) {
			return response('Lesson not found', 404);
		}

		$resources = Config::get('papi.resources');

		$breadcrumbs = [
			[
				'type'      => $resources['courses'],
				'id'        => $lesson->group->course->id,
				'name'      => $lesson->group->course->name,
				'ancestors' => [],
			],
			[
				'type'      => $resources['groups'],
				'id'        => $lesson->group->id,
				'name'      => $lesson->group->name,
				'ancestors' => [
					$resources['courses'] => $lesson->group->course->id,
				],
			],
			[
				'type'      => $resources['lessons'],
				'id'        => $lesson->id,
				'name'      => $lesson->name,
				'ancestors' => [
					$resources['courses'] => $lesson->group->course->id,
					$resources['groups']  => $lesson->group->id,
				],
			],
		];

		$items = [];

		$screens = $lesson->snippets()->with('slides')->get();

		foreach ($screens as $screen) {
			$items[] = [
				'type'       => $resources['screens'],
				'id'         => $screen->id,
				'name'       => $screen->name,
				'screenType' => $screen->type,
				'ancestors'  => [
					$resources['courses'] => $lesson->group->course->id,
					$resources['groups']   => $lesson->group->id,
					$resources['lessons']  => $lesson->id,
				],
			];

			if ($screen->type === 'slideshow') {
				$screenFirstSlide = $screen->slides->first();
				$sections = $screen->sections()->with('slides')->get();
				foreach ($sections as $section) {
					$sectionFirstSlide = $section->slides->first();
					$slideNumber = $sectionFirstSlide->id - $screenFirstSlide->id;
					$items[] = [
						'type'  => $resouces['sections'],
						'id'    => $section->id,
						'name'  => $section->name,
						'ancestors' => [
							$resources['courses'] => $lesson->group->course->id,
							$resources['groups']   => $lesson->group->id,
							$resources['lessons']  => $lesson->id,
							$resources['screens'] => $screen->id,
						],
						'slide' => $slideNumber,
					];
				}
			}
		}

		return response()->json(['breadcrumbs' => $breadcrumbs, 'items' => $items]);
	}
}
