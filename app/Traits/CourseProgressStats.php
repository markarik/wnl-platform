<?php

namespace App\Traits;

use App\Models\Flashcard;
use App\Models\Lesson;
use App\Models\QuizQuestion;
use App\Models\Section;
use App\Models\UserCourseProgress;
use App\Models\UserFlashcardsResults;
use App\Models\UserQuizResults;

trait CourseProgressStats {
	public function getCourseProgressStats($startDate, $endDate) {
		$start = microtime(true);
		$t = false;

		$allLessons = Lesson::select(['id'])
			->whereDate('created_at', '<=', $endDate)
			->where('is_required', 1)
			->count();

		if ($t) dump('allLessons', microtime(true) - $start);

		$timeCollection = $this->userTime()
			->whereBetween('created_at', [$startDate, $endDate])
			->orderBy('id', 'desc')
			->get();
		$userTime = (int)round(($timeCollection->max('time') - $timeCollection->min('time')) / 60);

		if ($t) dump('timeCollection', microtime(true) - $start);

		$userQuizQuestionsSolved = UserQuizResults
			::selectRaw('count(distinct(quiz_question_id)) as count')
			->where('user_id', $this->id)
			->first()
			->count;

		if ($t) dump('quizQuestionsSolved', microtime(true) - $start);

		$userSectionsProgress = UserCourseProgress::where('user_id', $this->profile->id)
			->whereDate('user_course_progress.created_at', '<=', $endDate)
			->join('lessons', 'lessons.id', '=', 'lesson_id')
			->join('sections', 'sections.id', '=', 'section_id')
			->where('lessons.is_required', 1)
			->whereNotNull('user_course_progress.section_id')
			->where('user_course_progress.status', 'complete')
			->count();

		if ($t) dump('sectionsProgress', microtime(true) - $start);

		$allSections = Section::select(['id'])
			->whereDate('sections.created_at', '<=', $endDate)
			->join('screens', 'screens.id', '=', 'screen_id')
			->join('lessons', 'lessons.id', '=', 'lesson_id')
			->where('lessons.is_required', 1)
			->count();

		$userSectionsProgressPercentage = (int)round(($userSectionsProgress / $allSections) * 100);

		if ($t) dump('allSections', microtime(true) - $start);

		$userFlashcardsSolved = UserFlashcardsResults
			::selectRaw('count(distinct(flashcard_id)) as count')
			->where('user_id', $this->id)
			->first()
			->count;

		if ($t) dump('userFlashcardsSolved', microtime(true) - $start);

		$end = microtime(true) - $start;
//		if ($end > 0.4) {
//			dump("User {$this->id} is slow, time {$end}");
//		}

		return [
			'quiz_questions_solved' => $userQuizQuestionsSolved,
			'flashcards_solved' => $userFlashcardsSolved,
			'sections_progress_perc' => $userSectionsProgressPercentage,
			'time' => $userTime
		];
	}

	public function hasFinishedCourse($startDate, $endDate) {
		$userStats = $this->getCourseProgressStats($startDate, $endDate);

		return $userStats['course_progress_perc'] >= 80
			&& $userStats['quiz_questions_solved_perc'] >= 60
			&& $userStats['time'] >= 300;
	}
}
