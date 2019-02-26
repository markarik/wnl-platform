<?php

namespace App\Traits;

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\QuizQuestion;
use App\Models\Section;
use App\Models\UserCourseProgress;
use App\Models\UserFlashcardsResults;
use App\Models\UserQuizResults;

trait CourseProgressStats
{
	public function getCourseProgressStats($startDate, $endDate)
	{
		/*
		 * Course progress (by completed lessons)
		 */
		$allLessons = Lesson::select(['id'])
			->whereDate('created_at', '<=', $endDate)
			->where('is_required', 1)
			->count();

		$userCourseProgress = UserCourseProgress::where('user_id', $this->profile->id)
			->whereDate('user_course_progress.created_at', '<=', $endDate)
			->join('lessons', 'lessons.id', '=', 'lesson_id')
			->where('lessons.is_required', 1)
			->whereNull('user_course_progress.section_id')
			->whereNull('user_course_progress.screen_id')
			->where('user_course_progress.status', 'complete')
			->count();

		$userCourseProgressPercentage = (int)round(($userCourseProgress / $allLessons) * 100);

		/*
		 * Time
		 */
		$timeCollection = $this->userTime()
			->whereBetween('created_at', [$startDate, $endDate])
			->orderBy('id', 'desc')
			->get();

		$userTime = (int)round(($timeCollection->max('time') - $timeCollection->min('time')) / 60);

		/*
		 * Quiz questions progress
		 */
		$userQuizQuestionsSolved = UserQuizResults
			::selectRaw('count(distinct(quiz_question_id)) as count')
			->where('user_id', $this->id)
			->first()
			->count;


		/*
		 * Sections progress
		 */
		$userSectionsProgress = UserCourseProgress::selectRaw('count(distinct(section_id)) as count')
			->where('user_id', $this->profile->id)
			->join('sections', 'sections.id', '=', 'section_id')
			->join('screens', 'screens.id', '=', 'sections.screen_id')
			->join('lessons', 'lessons.id', '=', 'screens.lesson_id')
			->where('lessons.is_required', 1)
			->where('user_course_progress.status', 'complete')
			->whereNotNull('user_course_progress.section_id')
			->whereDate('user_course_progress.created_at', '<=', $endDate)
			->first()
			->count;


		$allSections = Section::select(['sections.id'])
			->whereDate('sections.created_at', '<=', $endDate)
			->join('screens', 'screens.id', '=', 'screen_id')
			->join('lessons', 'lessons.id', '=', 'lesson_id')
			->where('lessons.is_required', 1)
			->count();

		$userSectionsProgressPercentage = (int)round(($userSectionsProgress / $allSections) * 100);

		/*
		 * Flashcards prgress
		 */
		$userFlashcardsSolved = UserFlashcardsResults
			::selectRaw('count(distinct(flashcard_id)) as count')
			->where('user_id', $this->id)
			->first()
			->count;

		return [
			'course_progress_perc'   => $userCourseProgressPercentage,
			'quiz_questions_solved'  => $userQuizQuestionsSolved,
			'time'                   => $userTime,
			'flashcards_solved'      => $userFlashcardsSolved,
			'sections_progress_perc' => $userSectionsProgressPercentage,
		];
	}

	public function hasFinishedCourse($startDate, $endDate)
	{
		$userStats = $this->getCourseProgressStats($startDate, $endDate);

		return $userStats['course_progress_perc'] >= 80
			&& $userStats['quiz_questions_solved_perc'] >= 60
			&& $userStats['time'] >= 300;
	}
}
