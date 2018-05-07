<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\UserCourseProgress;
use App\Models\UserLesson;
use Carbon\Carbon;
use DB;


class CalculateCoursePlan
{
	use Dispatchable;

	const GROUP_ID_WIECEJ_NIZ_LEK = 2;
	const GROUP_ID_WARSZTATY = 3;
	const GROUP_ID_POWTORKI = 11;
	const GROUP_ID_DODATKI = 15;
	const GROUP_ID_PROBNY_LEK = 14;

	protected $now;
	protected $user;
	protected $preset;
	protected $endDate;
	protected $startDate;
	protected $workDays;
	protected $workLoad;
	protected $sortedLessons;
	protected $sortedCompletedLessons;
	protected $sortedInProgressLessons;
	protected $requiredInProgressLessonsCount;
	protected $openNow;
	protected $openLastDay;
	protected $toBeScheduled;

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

	public function handle()
	{
		$plan = $this->calculatePlan()
			->map(function ($el) {
				return array_set($el, 'user_id', $this->user->id);
			});

		UserLesson::where('user_id', $this->user->id)->delete();
		DB::table('user_lesson')->insert($plan->toArray());

		return $plan;
	}

	private function calculatePlan()
	{
		$plan = collect();
		$startDate = $this->startDate;
		$workLoad = $this->workLoad;

		if ($workLoad === 0) {
			return $this->handleWorkloadZero($plan);
		}

		foreach ($this->openNow as $lesson) {
			$plan = $this->addToPlan($plan, $lesson->id, $this->now);
		}

		if ($this->preset === 'dateToDate') {
			$daysExcess = $this->daysQuantity % $this->toBeScheduled->count();
			$computedWorkLoad = floor($this->daysQuantity / $this->toBeScheduled->count());
			$lessonWithExtraDay = 0;
		}

		// Obowiązkowe
		foreach ($this->toBeScheduled as $lesson) {
			if ($this->preset === 'dateToDate' && $lessonWithExtraDay < $daysExcess) {
				$workLoad = $computedWorkLoad + 1;
				$lessonWithExtraDay++;
			} else if ($this->preset === 'dateToDate' && $lessonWithExtraDay >= $daysExcess) {
				$workLoad = $computedWorkLoad;
			}

			$dayOfWeekIso = $startDate->dayOfWeekIso;
			$isStartDateVariableAvailable = in_array($dayOfWeekIso, $this->workDays);

			if ($isStartDateVariableAvailable) {
				$plan = $this->addToPlan($plan, $lesson->id, $startDate);
			} else {
				while (!$isStartDateVariableAvailable) {
					$startDate->addDays(1);
					$dayOfWeekIso = $startDate->dayOfWeekIso;
					$isStartDateVariableAvailable = in_array($dayOfWeekIso, $this->workDays);
				}
				$plan = $this->addToPlan($plan, $lesson->id, $startDate);
			}
			$endDate = clone($startDate);
			$addedDays = 1;
			$enoughAdded = $addedDays >= $workLoad;
			$isEndDateAvailable = in_array($endDate->dayOfWeekIso, $this->workDays);
			while (!$isEndDateAvailable || !$enoughAdded) {
				$endDate->addDay();
				$addedDays++;
				$dayOfWeekIso = $endDate->dayOfWeekIso;
				$isEndDateAvailable = in_array($dayOfWeekIso, $this->workDays);
				$enoughAdded = $addedDays >= $workLoad;
			}
			$startDate = clone($endDate)->addDay();
		}

		foreach ($this->openLastDay as $lesson) {
			$plan = $this->addToPlan($plan, $lesson->id, $plan->last()->start_date);
		}

		return $plan;
	}

	protected function preprocessData()
	{
		$this->daysQuantity = $this->startDate->diffInDaysFiltered(function (Carbon $date) {
			$dayOfWeekIso = $date->dayOfWeekIso;

			return in_array($dayOfWeekIso, $this->workDays);
		}, $this->endDate->addDay());

		$builder = $this->user->lessonsAvailability()
			->orderBy('group_id')
			->orderBy('order_number');

		$notRequired = (clone $builder)
			->whereIn('group_id', [
				self::GROUP_ID_WIECEJ_NIZ_LEK,
				self::GROUP_ID_WARSZTATY,
				self::GROUP_ID_DODATKI,
			])
			->get();

		$openNow = (clone $builder)
			->join('user_course_progress', 'lessons.id', '=', 'user_course_progress.lesson_id')
			->whereNull('section_id')
			->whereNull('screen_id')
			->where('status', 'complete')
			->get()
			->merge($notRequired);

		$openLastDay = (clone $builder)
			->where('group_id', self::GROUP_ID_POWTORKI)
			->get();

		$toBeScheduled = (clone $builder)
			->whereNotIn('lessons.id', $openNow->merge($openLastDay)->pluck('id')->toArray())
			->get();

		$this->openNow = $openNow;
		$this->openLastDay = $openLastDay;
		$this->toBeScheduled = $toBeScheduled;
	}

	protected function handleWorkloadZero($plan)
	{
		foreach ($this->sortedLessons as $lesson) {
			$plan = $this->addToPlan($plan, $lesson->id, $this->now);
		}

		return $plan;
	}

	protected function addToPlan($plan, $lessonId, $date)
	{
		return $plan->push([
			'lesson_id'  => $lessonId,
			'start_date' => $date,
		]);
	}
}
