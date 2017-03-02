<?php

namespace App\Http\Controllers\Api;

use App\Models\Lesson;
use App\Models\Snippet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class LessonsApiController extends Controller
{
	// TODO: Feb 19, 2017 - It's ugly how we compose the navigation... Should be easier.
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
				'meta' => [],
			],
			[
				'type'      => $resources['groups'],
				'id'        => $lesson->group->id,
				'name'      => $lesson->group->name,
				'ancestors' => [
					$resources['courses'] => $lesson->group->course->id,
				],
				'meta' => [],
			],
			[
				'type'      => $resources['lessons'],
				'id'        => $lesson->id,
				'name'      => $lesson->name,
				'ancestors' => [
					$resources['courses'] => $lesson->group->course->id,
					$resources['groups']  => $lesson->group->id,
				],
				'meta' => [],
			],
		];

		$items = [];

		$screens = $lesson->snippets()->with('slides')->get();

		foreach ($screens as $screen) {
			$items[] = [
				'type'       => $resources['screens'],
				'id'         => $screen->id,
				'name'       => $screen->name,
				'ancestors'  => [
					$resources['courses'] => $lesson->group->course->id,
					$resources['groups']   => $lesson->group->id,
					$resources['lessons']  => $lesson->id,
				],
				'meta' => [
					'screenType' => $screen->type,
				],
			];

			if ($screen->type === 'slideshow') {
				$screenFirstSlide = $screen->slides->first();
				$sections = $screen->sections()->with('slides')->get();
				foreach ($sections as $section) {
					$sectionFirstSlide = $section->slides->first();
					$slideNumber = $sectionFirstSlide->id - $screenFirstSlide->id + 1;
					$items[] = [
						'type'  => $resources['sections'],
						'id'    => $section->id,
						'name'  => $section->name,
						'ancestors' => [
							$resources['courses'] => $lesson->group->course->id,
							$resources['groups']   => $lesson->group->id,
							$resources['lessons']  => $lesson->id,
							$resources['screens'] => $screen->id,
						],
						'meta' => [
							'slide' => $slideNumber,
						],
					];
				}
			}
		}

		return response()->json(['breadcrumbs' => $breadcrumbs, 'items' => $items]);
	}

	public function getScreen($snippetId) {
		$screen = Snippet::find($snippetId);

		return response()->json(['screen' => $screen['attributes']]);
	}
}
