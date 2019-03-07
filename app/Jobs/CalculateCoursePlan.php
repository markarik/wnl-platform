<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserCourseProgress;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\UserLesson;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;


class CalculateCoursePlan
{
	use Dispatchable;

	protected $now;
	/** @var User */
	protected $user;
	protected $preset;
	/** @var Carbon */
	protected $endDate;
	/** @var Carbon */
	protected $startDate;
	protected $workDays;
	protected $workLoad;
	protected $sortedLessonIds;
	protected $openNowLessonIds;
	/** @var Collection */
	protected $toBeScheduled;
	protected $daysQuantity;

	public function __construct($user, $options)
	{
		$this->user = $user;
		$this->startDate = $options['startDate'];
		$this->endDate = $options['endDate'];
		$this->workLoad = $options['workLoad'];
		$this->workDays = $options['workDays'];
		$this->preset = $options['preset'];
		$this->now = Carbon::now();

		$this->preprocessData();
	}

	/**
	 * @return Collection
	 * @throws \Exception
	 */
	public function handle()
	{
		DB::beginTransaction();

		try {
			$plan = $this->calculatePlan()
				->map(function ($el) {
					$el['start_date']->setTimezone('UTC');
					return array_set($el, 'user_id', $this->user->id);
				});

			UserLesson::where('user_id', $this->user->id)->delete();
			DB::table('user_lesson')->insert($plan->toArray());
		} catch (\Exception $e) {
			DB::rollBack();
			throw $e;
		}

		DB::commit();

		return $plan;
	}

	private function calculatePlan()
	{
		$plan = collect();
		$startDate = $this->startDate;
		$workLoad = $this->workLoad;
		$toBeScheduledCount = $this->toBeScheduled->count();
		$lessonWithExtraDay = 0;
		$computedWorkLoad = 0;
		$daysExcess = 0;

		if ($this->preset === 'default') {
			return $this->handleDefaultPlan();
		}

		if ($this->preset === 'openAll' || $toBeScheduledCount === 0) {
			return $this->handleWorkloadZero($plan);
		}

		foreach ($this->openNowLessonIds as $lessonId) {
			$plan = $this->addToPlan($plan, $lessonId, $this->now);
		}

		if ($this->preset === 'dateToDate') {
			$daysExcess = $this->daysQuantity % $toBeScheduledCount;
			$computedWorkLoad = floor($this->daysQuantity / $toBeScheduledCount);
		}

		foreach ($this->toBeScheduled as $lesson) {
			if ($this->preset === 'dateToDate' &&
				$lessonWithExtraDay < $daysExcess
			) {
				$workLoad = $computedWorkLoad + 1;
				$lessonWithExtraDay++;
			} else if ($this->preset === 'dateToDate' &&
				$lessonWithExtraDay >= $daysExcess
			) {
				$workLoad = $computedWorkLoad;
			}

			$startDateAvailable = $this->checkDay($startDate->dayOfWeekIso);

			if ($startDateAvailable) {
				$plan = $this->addToPlan($plan, $lesson->id, $startDate);
			} else {
				while (!$startDateAvailable) {

					$startDate->addDays(1);
					$startDateAvailable = $this->checkDay($startDate->dayOfWeekIso);
				}
				$plan = $this->addToPlan($plan, $lesson->id, $startDate);
			}

			$endDate = clone($startDate);
			$addedDays = 1;
			$enoughAdded = $addedDays >= $workLoad;
			$isEndDateAvailable = $this->checkDay($endDate->dayOfWeekIso);

			while (!$isEndDateAvailable || !$enoughAdded) {
				$endDate->addDay();
				if ($isEndDateAvailable) $addedDays++;
				$isEndDateAvailable = $this->checkDay($endDate->dayOfWeekIso);
				$enoughAdded = $addedDays >= $workLoad;
			}

			$startDate = clone($endDate)->addDay();
		}

		return $plan;
	}

	protected function preprocessData()
	{
		if ($this->workDays) {
			$this->daysQuantity = $this->startDate->diffInDaysFiltered(
				function ($date) {
					return in_array($date->dayOfWeekIso, $this->workDays);
				},
				(clone $this->endDate)->addDay());
		}

		$userLessons = $this->user->getLessonsAvailability();
		$userLessonIds = $userLessons->pluck('id')->toArray();
		$notRequiredLessonIds = $userLessons->filter(function($lesson) { return !$lesson->is_required; })->pluck('id')->toArray();

		$openNowLessonIds = UserCourseProgress::whereIn('lesson_id', $userLessonIds)
			->where('user_id', $this->user->profile->id)
			->whereNull('section_id')
			->whereNull('screen_id')
			->where('status', 'complete')
			->get()
			->pluck('lesson_id')
			->merge($notRequiredLessonIds)
			->unique()
			->toArray();

		$this->toBeScheduled = $userLessons->filter(function($lesson) use ($openNowLessonIds) { return !in_array($lesson->id, $openNowLessonIds); });
		$this->sortedLessonIds = $userLessonIds;
		$this->openNowLessonIds = $openNowLessonIds;
	}

	protected function handleDefaultPlan()
	{
		dispatch_now(new ArchiveCoursePlan($this->user, true));

		return Collection::make();
	}

	protected function handleWorkloadZero($plan)
	{
		foreach ($this->sortedLessonIds as $lessonId) {
			$plan = $this->addToPlan($plan, $lessonId, $this->now);
		}

		return $plan;
	}

	/**
	 * @param Collection $plan
	 * @param int $lessonId
	 * @param Carbon $date
	 * @return Collection
	 */
	protected function addToPlan($plan, $lessonId, $date)
	{
		return $plan->push([
			'lesson_id'  => $lessonId,
			'start_date' => $date,
		]);
	}

	protected function checkDay(int $day)
	{
		return in_array($day, $this->workDays);
	}
}
