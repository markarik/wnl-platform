<?php

namespace App\Traits;

use App\Models\Lesson;
use App\Models\QuizQuestion;
use App\Models\UserCourseProgress;
use App\Models\UserQuizResults;

trait CourseProgressStats {
	public function getCourseProgressStats($startDate, $endDate) {
		$allLessons = Lesson::select(['id'])
			->whereDate('created_at', '<=', $endDate)
			->where('is_required', 1)
			->count();
		$allQuestions = QuizQuestion::whereDate('created_at', '<=', $endDate)->count();
		$timeCollection = $this->userTime()
			->whereBetween('created_at', [$startDate, $endDate])
			->orderBy('id', 'desc')
			->get();
		$userTime = (int)round(($timeCollection->max('time') - $timeCollection->min('time')) / 60);

		$userCourseProgress = UserCourseProgress::where('user_id', $this->profile->id)
			->whereDate('user_course_progress.created_at', '<=', $endDate)
			->join('lessons', 'lessons.id', '=', 'lesson_id')
			->where('lessons.is_required', 1)
			->whereNull('user_course_progress.section_id')
			->whereNull('user_course_progress.screen_id')
			->where('user_course_progress.status', 'complete')
			->count();

		$userCourseProgressPercentage = (int)round(($userCourseProgress / $allLessons) * 100);

		$userQuizQuestionsSolved = UserQuizResults::where('user_id', $this->id)
			->groupBy('quiz_question_id')
			->get(['quiz_question_id'])
			->count();

		$userQuizQuestionsSolvedPercentage = (int)round(($userQuizQuestionsSolved / $allQuestions) * 100);

		return [
			'quiz_questions_solved_perc' => $userQuizQuestionsSolvedPercentage,
			'course_progress_perc' => $userCourseProgressPercentage,
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
