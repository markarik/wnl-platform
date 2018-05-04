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

		foreach ($this->sortedCompletedLessons as $lesson) {
			$plan = $this->addToPlan($plan, $lesson->id, $this->now);
		}

		if ($this->preset === 'dateToDate') {
			$daysExcess = $this->daysQuantity % $this->requiredInProgressLessonsCount;
			$computedWorkLoad = floor($this->daysQuantity / $this->requiredInProgressLessonsCount);
			$lessonWithExtraDay = 0;
		}

		foreach ($this->sortedInProgressLessons as $lesson) {
			$groupId = $lesson->group_id;
			// Nieobowiązkowe
			if (!self::isGroupObligatory($groupId)) {
				$plan = $this->addToPlan($plan, $lesson->id, $this->now);
			} elseif ($groupId !== self::GROUP_ID_POWTORKI) {
				// Obowiązkowe
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
		}


		$filteredLessons = $this->sortedInProgressLessons
			->where('group_id', self::GROUP_ID_POWTORKI);

		foreach ($filteredLessons->pluck('id') as $lessonId) {
			$plan = $this->addToPlan($plan, $lessonId, $plan->last()->start_date);
		}

		return $plan;
	}

	protected function preprocessData()
	{
		$this->daysQuantity = $this->startDate->diffInDaysFiltered(function (Carbon $date) {
			$dayOfWeekIso = $date->dayOfWeekIso;

			return in_array($dayOfWeekIso, $this->workDays);
		}, $this->endDate->addDay());

		$this->sortedLessons = $this->user->lessonsAvailability()
			->orderBy('group_id')
			->orderBy('order_number')
			->get();

		$completeLessons = UserCourseProgress::where('user_id', $this->user->profile->id)
			->whereNull('section_id')
			->whereNull('screen_id')
			->where('status', 'complete')
			->get()
			->pluck('lesson_id')
			->toArray();

		$this->sortedCompletedLessons = collect();
		$this->sortedInProgressLessons = collect();
		$this->requiredInProgressLessonsCount = 0;

		foreach ($this->sortedLessons as $lesson) {
			if (in_array($lesson->id, $completeLessons)) {
				$this->sortedCompletedLessons->push($lesson);
			} else {
				$this->sortedInProgressLessons->push($lesson);
				if ($lesson->is_required === 1) {
					$this->requiredInProgressLessonsCount++;
				}
			}
		}
	}

	public static function isGroupObligatory($groupId)
	{
		return
			$groupId !== self::GROUP_ID_WIECEJ_NIZ_LEK &&
			$groupId !== self::GROUP_ID_WARSZTATY &&
			$groupId !== self::GROUP_ID_DODATKI;
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

	protected function handleDateToDate() {
		$startDate = $this->startDate;
		$workLoad = $this->workLoad;


	}
}
