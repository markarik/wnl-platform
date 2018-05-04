<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Console\Commands\LessonUser;
use DB;
use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
use Cache;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use App\Http\Requests\User\UpdateUserLesson;
use App\Http\Requests\User\UpdateLessonsPreset;
use App\Models\UserCourseProgress;
use App\Models\UserLesson;
use App\Models\User;
use App\Http\Controllers\Api\Transformers\LessonTransformer;
use League\Fractal\Resource\Collection;

class UserLessonApiController extends ApiController
{
	const GROUP_ID_WIECEJ_NIZ_LEK = 2;
	const GROUP_ID_WARSZTATY = 3;
	const GROUP_ID_POWTORKI = 11;
	const GROUP_ID_DODATKI = 15;
	const GROUP_ID_PROBNY_LEK = 14;

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-lesson');
	}

	public function put(UpdateUserLesson $request)
	{
		$userId = $request->userId;
		$lessonId = $request->lessonId;

		$userLesson = UserLesson::where([
			'lesson_id' => $lessonId,
			'user_id'   => $userId,
		])->first();


		if (!$userLesson) {
			return $this->respondNotFound();
		}

		$userLesson->update([
			'start_date' => Carbon::parse($request->input('date')),
		]);

		Cache::tags("user-$userId")->flush();

		return $this->respondOk();
	}

	public function putPlan(UpdateLessonsPreset $request, $userId)
	{
		$user = User::find($userId);
		$profileId = $user->profile->id;
		$workLoad = $request->work_load;
		$workDays = $request->work_days;
		$startDate = Carbon::parse($request->start_date);
		$endDate = Carbon::parse($request->end_date);

		list($daysQuantity, $presetActive, $sortedLessons, $sortedCompletedLessons,
			$sortedInProgressLessons, $requiredInProgressLessonsCount)
			= $this->preprocessData($request, $startDate, $workDays, $endDate, $user, $profileId);

		$plan = $this->calculatePlan(
			$sortedLessons,
			$sortedCompletedLessons,
			$requiredInProgressLessonsCount,
			$sortedInProgressLessons,
			$startDate,
			$daysQuantity,
			$workDays,
			$workLoad,
			$presetActive
		)->map(function ($el) use ($user) {
			return array_set($el, 'user_id', $user->id);
		});
		
		UserLesson::where('user_id', $user->id)->delete();
		DB::table('user_lesson')->insert($plan->toArray());

		$data = (new LessonsApiController($request))->transform($user->lessonsAvailability);

		return $this->respondOk([
			'lessons'        => $data,
			'end_date'       => $plan->last()['start_date']->timestamp,
			'end_date_human' => $plan->last()['start_date'],
		]);
	}

	private function calculatePlan(
		$sortedLessons,
		$sortedCompletedLessons,
		$requiredInProgressLessonsCount,
		$lessons,
		$startDate,
		$daysQuantity,
		$workDays,
		$workLoad,
		$presetActive
	)
	{
		$plan = collect();
		$now = Carbon::now();

		if ($workLoad === 0) {
			foreach ($sortedLessons as $lesson) {
				$plan->push([
					'lesson_id'  => $lesson->id,
					'start_date' => $now,
				]);
			}

			return $plan;
		}

		foreach ($sortedCompletedLessons as $lesson) {
			$plan->push([
				'lesson_id'  => $lesson->id,
				'start_date' => $now,
			]);
		}

		if ($presetActive === 'dateToDate') {
			$daysExcess = $daysQuantity % $requiredInProgressLessonsCount;
			$computedWorkLoad = floor($daysQuantity / $requiredInProgressLessonsCount);
			$lessonWithExtraDay = 0;
		}

		foreach ($lessons as $lesson) {
			$groupId = $lesson->group_id;
			if (!self::isGroupObligatory($groupId)) {
				$plan->push([
					'lesson_id'  => $lesson->id,
					'start_date' => $now,
				]);
			} elseif ($groupId !== self::GROUP_ID_POWTORKI) {
				if ($presetActive === 'dateToDate' && $lessonWithExtraDay < $daysExcess) {
					$workLoad = $computedWorkLoad + 1;
					$lessonWithExtraDay++;
				} else if ($presetActive === 'dateToDate' && $lessonWithExtraDay >= $daysExcess) {
					$workLoad = $computedWorkLoad;
				}
				$dayOfWeekIso = $startDate->dayOfWeekIso;
				$isStartDateVariableAvailable = in_array($dayOfWeekIso, $workDays);

				if ($isStartDateVariableAvailable) {
					$plan->push([
						'lesson_id'  => $lesson->id,
						'start_date' => $startDate,
					]);
				} else {
					while (!$isStartDateVariableAvailable) {
						$startDate->addDays(1);
						$dayOfWeekIso = $startDate->dayOfWeekIso;
						$isStartDateVariableAvailable = in_array($dayOfWeekIso, $workDays);
					}
					$plan->push([
						'lesson_id'  => $lesson->id,
						'start_date' => $startDate,
					]);
				}
				$endDate = clone($startDate);
				$addedDays = 1;
				$enoughAdded = $addedDays >= $workLoad;
				$isEndDateAvailable = in_array($endDate->dayOfWeekIso, $workDays);
				while (!$isEndDateAvailable || !$enoughAdded) {
					$endDate->addDay();
					$addedDays++;
					$dayOfWeekIso = $endDate->dayOfWeekIso;
					$isEndDateAvailable = in_array($dayOfWeekIso, $workDays);
					$enoughAdded = $addedDays >= $workLoad;
				}
				$startDate = clone($endDate)->addDay();
			}
		}

		$lastLessonStartDate = $startDate->subDay($workLoad);

		$filteredLessons = array_filter($lessons, (function ($lesson) {
			return $lesson->group_id === self::GROUP_ID_POWTORKI;
		}));

		$filteredLessonsIds = array_map(function ($lesson) {
			return $lesson->id;
		}, $filteredLessons);

		foreach ($filteredLessonsIds as $lessonId) {
			$plan->push([
				'lesson_id'  => $lessonId,
				'start_date' => $lastLessonStartDate,
			]);
		}

		return $plan;
	}

	protected function preprocessData(UpdateLessonsPreset $request, $startDate, $workDays, $endDate, $user, $profileId): array
	{
		$daysQuantity = $startDate->diffInDaysFiltered(function (Carbon $date) use ($workDays) {
			$dayOfWeekIso = $date->dayOfWeekIso;

			return in_array($dayOfWeekIso, $workDays);
		}, $endDate->addDay());

		$presetActive = $request->preset_active;
		$sortedLessons = $user->lessonsAvailability()
			->orderBy('group_id')
			->orderBy('order_number')
			->get();

		$completeLessons = UserCourseProgress::where('user_id', $profileId)
			->whereNull('section_id')
			->whereNull('screen_id')
			->where('status', 'complete')
			->get()
			->pluck('lesson_id')
			->toArray();

		$sortedCompletedLessons = [];
		$sortedInProgressLessons = [];
		$requiredInProgressLessonsCount = 0;

		foreach ($sortedLessons as $lesson) {
			if (in_array($lesson->id, $completeLessons)) {
				array_push($sortedCompletedLessons, $lesson);
			} else {
				array_push($sortedInProgressLessons, $lesson);
				if ($lesson->is_required === 1) {
					$requiredInProgressLessonsCount++;
				}
			}
		};

		return array($daysQuantity, $presetActive, $sortedLessons, $sortedCompletedLessons, $sortedInProgressLessons, $requiredInProgressLessonsCount);
	}

	public static function isGroupObligatory($groupId)
	{
		return
			$groupId !== self::GROUP_ID_WIECEJ_NIZ_LEK &&
			$groupId !== self::GROUP_ID_WARSZTATY &&
			$groupId !== self::GROUP_ID_DODATKI;
	}
}
