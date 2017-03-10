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

		$editionStructure = [
			'id' => $course->id,
			'name' => $course->name,
			'structure' => [
				$resources['groups'] => [],
				$resources['lessons'] => []
			],
		];

		$groups = $course->groups()->with('lessons')->get();

		foreach ($groups as $group) {
			$groupStructure = [
				'id' => $group->id,
				'name' => $group->name,
				'course' => $course->id,
				$resources['lessons'] => []
			];

			foreach ($group->lessons as $lesson) {
				array_push($groupStructure[$resources['lessons']], $lesson->id);

				$lessonStructure = [
					'id' => $lesson->id,
					'name' => $lesson->name,
					'course' => $course->id,
					'group' => $group->id,
					'isAvailable' => $lesson->id < 4,
					$resources['screens'] => [],
				];

				$screens = $lesson->snippets()->with('slides')->get();
				foreach ($screens as $screen) {
					$screenStructure = [
						'id' => $screen->id,
						'name' => $screen->name,
						'type' => $screen->type,
						'course' => $course->id,
						'groups' => $group->id,
						'lesson' => $lesson->id,
					];

					$sectionBasis = [
						'course' => $course->id,
						'group' => $group->id,
						'lesson' => $lesson->id,
						'screen' => $screen->id,
					];

					$screenStructure[$resources['sections']] = $this->getSectionsForScreen($sectionBasis, $screen);
					$lessonStructure[$resources['screens']][] = $screenStructure;
				}

				$editionStructure['structure'][$resources['lessons']][$lesson->id] = $lessonStructure;
			}

			$editionStructure['structure'][$resources['groups']][] = $groupStructure;
		}

		return response()->json($editionStructure);
	}

	private function getSectionsForScreen($basis, $screen) {
		$sectionsStructure = [];

		if ($screen->type === 'slideshow') {
			$screenFirstSlide = $screen->slides->first();
			$sections = $screen->sections()->with('slides')->get();
			foreach ($sections as $section) {
				$sectionFirstSlide = $section->slides->first();
				$slideNumber = $sectionFirstSlide->id - $screenFirstSlide->id + 1;

				$sectionsStructure[] = array_merge($basis, [
					'id' => $section->id,
					'name' => $section->name,
					'slide' => $slideNumber,
				]);
			}
		}

		return $sectionsStructure;
	}
}
