<?php

namespace App\Jobs;

use App\Http\Controllers\Api\PrivateApi\CoursesApiController;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
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
	/** @var User */
	protected $user;
	protected $preset;
	protected $endDate;
	protected $startDate;
	protected $workDays;
	protected $workLoad;
	protected $sortedLessons;
	protected $openNow;
	protected $openLastDay;
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
			return $this->handleDefaultPlan($plan);
		}

		if ($this->preset === 'openAll' || $toBeScheduledCount === 0) {
			return $this->handleWorkloadZero($plan);
		}

		foreach ($this->openNow as $lesson) {
			$plan = $this->addToPlan($plan, $lesson->id, $this->now);
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

		foreach ($this->openLastDay as $lesson) {
			$plan = $this->addToPlan($plan, $lesson->id, $plan->last()['start_date']);
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

		$builder = $this->user->lessonsAvailabilityUnordered()
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
			->join('user_course_progress', function ($join) {
				$join->on('lessons.id', '=', 'user_course_progress.lesson_id');
			})
			->where('user_course_progress.user_id', $this->user->profile->id)
			->whereNull('section_id')
			->whereNull('screen_id')
			->where('status', 'complete')
			->where('group_id', '!=', self::GROUP_ID_POWTORKI)
			->get()
			->merge($notRequired);

		$openLastDay = (clone $builder)
			->where('group_id', self::GROUP_ID_POWTORKI)
			->get();

		$notScheduled = $openNow->merge($openLastDay)->pluck('id')->toArray();
		$toBeScheduled = (clone $builder)
			->whereNotIn('lessons.id', $notScheduled)
			->get();

		$this->sortedLessons = $builder->get();
		$this->openNow = $openNow;
		$this->openLastDay = $openLastDay;
		$this->toBeScheduled = $toBeScheduled;
	}

	protected function handleDefaultPlan($plan)
	{
		// TODO remove the custom plan
		$productsIds = $this->user->orders()->where('paid', 1)->get(['product_id']);
		$lessonsWithStartDates = \DB::table('lesson_product')
			->whereIn('product_id', $productsIds)
			->orderBy('start_date', 'desc')
			->get()
			->unique('lesson_id');

		foreach ($lessonsWithStartDates as $entry) {
			$plan = $this->addToPlan($plan, $entry->lesson_id, Carbon::parse($entry->start_date));
		}

		return $plan;
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

	protected function checkDay(int $day)
	{
		return in_array($day, $this->workDays);
	}
}
