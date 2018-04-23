<?php namespace App\Http\Controllers\Api\PrivateApi;

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

class UserLessonApiController extends ApiController
{
	const GROUP_ID_WIECEJ_NIZ_LEK = 2;
	const GROUP_ID_WARSZTATY = 3;
	const GROUP_ID_POWTORKI = 11;
	const GROUP_ID_DODATKI = 15;

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
			'user_id' => $userId
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
		$daysQuantity = $endDate->diffinDays($startDate);
		$presetActive = $request->preset_active;
		$subscriptionDates = $user->getSubscriptionDatesAttribute();
		$subscriptionEndDate = now()->setTimestamp($subscriptionDates["max"]);
		$sortedLessons = $user->lessonsAvailability()
			->orderBy('group_id')
			->orderBy('order_number')
			->get();

		$userCourseProgress = UserCourseProgress::where('user_id', $profileId);
		$completeLessons = (clone $userCourseProgress)
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

		if ($workLoad === 0) {
			foreach ($sortedLessons as $lesson) {
				$lessonId = $lesson->id;
				DB::table('user_lesson')
					->where('lesson_id', $lessonId)
					->update(['start_date' => Carbon::now()]);
			};
		} else {
			foreach ($sortedCompletedLessons as $lesson) {
				$lessonId = $lesson->id;
				DB::table('user_lesson')
					->where('lesson_id', $lessonId)
					->update(['start_date' => Carbon::now()]);
			};

			UserLessonApiController::calculatePlan(
				$requiredInProgressLessonsCount,
				$sortedInProgressLessons,
				$startDate,
				$daysQuantity,
				$workDays,
				$workLoad,
				$presetActive
			);
		}
	}

	private function calculatePlan(
		$requiredInProgressLessonsCount,
		$lessons,
		$startDate,
		$daysQuantity,
		$workDays,
		$workLoad,
		$presetActive
	)
	{
		if ($presetActive === 'dateToDate') {
			$daysExcess = $daysQuantity % $requiredInProgressLessonsCount;
			$computedWorkLoad = floor($daysQuantity / $requiredInProgressLessonsCount);
			$lessonWithExtraDay = 0;
		}

		foreach ($lessons as $lesson) {
			$lessonId = $lesson->id;
			$queriedLesson = DB::table('user_lesson')
				->where('lesson_id', $lessonId);
			$groupId = $lesson->group_id;

			if ($groupId === self::GROUP_ID_WIECEJ_NIZ_LEK ||
				$groupId === self::GROUP_ID_WARSZTATY ||
				$groupId === self::GROUP_ID_DODATKI) {
				$queriedLesson->update(['start_date' => Carbon::now()]);
			} else {
				if ($presetActive === 'dateToDate' && $lessonWithExtraDay < $daysExcess) {
					$workLoad = $computedWorkLoad + 1;
					echo('if');
					$lessonWithExtraDay++;
				} else if ($presetActive === 'dateToDate' && $lessonWithExtraDay >= $daysExcess) {
					echo('else if');
					$workLoad = $computedWorkLoad;
				}
				$dayOfWeekIso = $startDate->dayOfWeekIso;
				$isStartDateVariableAvilable = in_array($dayOfWeekIso, $workDays);

				if ($isStartDateVariableAvilable) {
					$queriedLesson
						->update(['start_date' => $startDate]);
				} else {
					while (!$isStartDateVariableAvilable) {
						$startDate->addDays(1);
						$dayOfWeekIso = $startDate->dayOfWeekIso;
						$isStartDateVariableAvilable = in_array($dayOfWeekIso, $workDays);
					}
					$queriedLesson
						->update(['start_date' => $startDate]);
				}
				$startDate->addHours($workLoad * 24);
			}
		}
		// dodaje sie jeden dzień do startDate - nie jest taki sam jak próbny lek
		foreach ($lessons as $lesson) {
			if ($lesson->group_id === self::GROUP_ID_POWTORKI) {
				DB::table('user_lesson')
					->where('lesson_id', $lesson->id)
					->update(['start_date' => $startDate]);
			}
		}
	}
}
