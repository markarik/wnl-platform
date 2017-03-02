<?php

namespace App\Http\Controllers\Api;

use App\Models\Lesson;
use App\Models\Snippet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ComposeSidenavItems;
use Illuminate\Support\Facades\Config;

class LessonsApiController extends Controller
{
	use ComposeSidenavItems;

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
			$this->composeItem(
				$resources['courses'],
				$lesson->group->course->id,
				$lesson->group->course->name
			),
			$this->composeItem(
				$resources['groups'],
				$lesson->group->id,
				$lesson->group->name,
				[
					$resources['courses'] => $lesson->group->course->id,
				]
			),
			$this->composeItem(
				$resources['lessons'],
				$lesson->id,
				$lesson->name,
				[
					$resources['courses'] => $lesson->group->course->id,
					$resources['groups']  => $lesson->group->id,
				]
			),
		];

		$items = [];

		$screens = $lesson->snippets()->with('slides')->get();

		foreach ($screens as $screen) {
			$items[] = $this->composeItem(
				$resources['screens'],
				$screen->id,
				$screen->name,
				[
					$resources['courses'] => $lesson->group->course->id,
					$resources['groups']  => $lesson->group->id,
					$resources['lessons'] => $lesson->id,
				],
				[
					'screenType' => $screen->type,
				]
			);

			if ($screen->type === 'slideshow') {
				$screenFirstSlide = $screen->slides->first();
				$sections = $screen->sections()->with('slides')->get();
				foreach ($sections as $section) {
					$sectionFirstSlide = $section->slides->first();
					$slideNumber = $sectionFirstSlide->id - $screenFirstSlide->id + 1;
					$items[] = $this->composeItem(
						$resources['sections'],
						$section->id,
						$section->name,
						[
							$resources['courses'] => $lesson->group->course->id,
							$resources['groups']   => $lesson->group->id,
							$resources['lessons']  => $lesson->id,
							$resources['screens'] => $screen->id,
						],
						[
							'slide' => $slideNumber,
						]
					);
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
