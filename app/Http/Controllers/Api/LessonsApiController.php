<?php

namespace App\Http\Controllers\Api;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

		$breadcrumbs = [
			[
				'type' => 'course',
				'icon' => 'course',
				'url'  => route('course', $lesson->id),
				'text' => $lesson->group->course->name,
			],
			[
				'type' => 'group',
				'icon' => 'group',
				'url'  => '#',
				'text' => $lesson->group->name,
			],
			[
				'type' => 'lesson',
				'icon' => 'lesson',
				'url'  => route('lesson', [$lesson->group->course->id, $lesson->id]),
				'text' => $lesson->name,
			],
		];
		$navigation = [];

		$snippets = $lesson->snippets()->with('slides')->get();

		foreach ($snippets as $snippet) {
			if ($snippet->type === 'slideshow') {
				$snippetFirstSlide = $snippet->slides->first();
			}
			$navigation[] = [
				'type'       => 'snippet',
				'snipetType' => $snippet->type,
				'icon'       => 'snippet',
				'url'        => '#',
				'text'       => $snippet->name,
				'state'      => 'California',
			];

			$sections = $snippet->sections()->with('slides')->get();
			foreach ($sections as $section) {
				if ($snippet->type === 'slideshow') {
					$sectionFirstSlide = $section->slides->first();
					$slideNumber = $sectionFirstSlide->id - $snippetFirstSlide->id;
					$url = route('section', [$lesson->group->course->id, $lesson->id, $snippet->id, $slideNumber]);
				} else {
					$url = '#';
				}

				$navigation[] = [
					'type'  => 'section',
					'icon'  => 'section',
					'url'   => $url,
					'text'  => $section->name,
					'state' => 'California',
				];
			}
		}

//		return view('layouts.guest');

		return response()->json(['breadcrumbs' => $breadcrumbs, 'navigation' => $navigation]);
	}
}
