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

	private $resources;

	/**
	 * @return string/json
	 */
	public function getNavigation($courseId)
	{
		$course = Course::find($courseId);
		if (!$course) {
			return response('Course not found', 404);
		}

		$this->resources = Config::get('papi.resources');

		$editionStructure = [
			'id' => $course->id,
			'name' => $course->name,
			$this->resources['groups'] => [],
			'structure' => [
				$this->resources['groups'] => [],
				$this->resources['lessons'] => [],
				$this->resources['screens'] => [],
				$this->resources['sections'] => [],
			],
		];

		$groups = $course->groups()->with('lessons')->get();

		foreach ($groups as $group) {
			array_push($editionStructure[$this->resources['groups']], $group->id);

			$groupStructure = [
				'id' => $group->id,
				'name' => $group->name,
				'course' => $course->id,
				$this->resources['lessons'] => []
			];

			foreach ($group->lessons as $lesson) {
				array_push($groupStructure[$this->resources['lessons']], $lesson->id);

				$lessonStructure = [
					'id' => $lesson->id,
					'name' => $lesson->name,
					'course' => $course->id,
					'group' => $group->id,
					'isAvailable' => $lesson->id < 3,
					$this->resources['screens'] => [],
				];

				$screens = $lesson->snippets()->with('slides')->get();
				foreach ($screens as $screen) {
					array_push($lessonStructure[$this->resources['screens']], $screen->id);

					$screenStructure = [
						'id' => $screen->id,
						'name' => $screen->name,
						'type' => $screen->type,
						'course' => $course->id,
						'groups' => $group->id,
						'lesson' => $lesson->id,
						$this->resources['sections'] => [],
					];

					$sectionBasis = [
						'course' => $course->id,
						'group' => $group->id,
						'lesson' => $lesson->id,
						'screen' => $screen->id,
					];

					$this->getSectionsForScreen(
						$editionStructure['structure'][$this->resources['sections']],
						$screen,
						$screenStructure,
						$sectionBasis
					);
					$editionStructure['structure'][$this->resources['screens']][$screen->id] = $screenStructure;
				}

				$editionStructure['structure'][$this->resources['lessons']][$lesson->id] = $lessonStructure;
			}

			$editionStructure['structure'][$this->resources['groups']][$group->id] = $groupStructure;
		}

		return response()->json($editionStructure);
	}

	private function getSectionsForScreen(&$allSections, $screen, &$screenStructure, $basis) {
		if ($screen->type === 'slideshow') {
			$screenFirstSlide = $screen->slides->first();
			$sections = $screen->sections()->with('slides')->get();
			foreach ($sections as $section) {
				array_push($screenStructure[$this->resources['sections']], $section->id);

				$sectionFirstSlide = $section->slides->first();
				$slideNumber = $sectionFirstSlide->id - $screenFirstSlide->id + 1;

				$allSections[$section->id] = array_merge($basis, [
					'id' => $section->id,
					'name' => $section->name,
					'slide' => $slideNumber,
				]);
			}
		}
	}
}
