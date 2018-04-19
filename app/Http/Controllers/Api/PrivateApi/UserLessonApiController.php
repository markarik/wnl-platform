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

	public function putPlan(UpdateLessonsPreset $request)
	{
		$userId = $request->userId;
		$user = User::find($userId);
		$profileId = $user->profile->id;
		$workLoad = $request->work_load;
		$workDays = $request->work_days;
		$daysQuantity = $request->days_quantity;
		$presetStartDate = Carbon::parse($request->preset_start_date);
		$startDate = Carbon::parse($request->start_date);
		$endDate = Carbon::parse($request->end_date);
		$subscriptionDateTimestamp = $user->getSubscriptionDatesAttribute();
		$subscriptionEndDate = Carbon::createFromTimestamp($subscriptionDateTimestamp["max"]);
		$daysLeft = $startDate->diffInDays($endDate);
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

		$sortedCompletedLessons = $sortedLessons->filter(function($sortedLesson) use ($completeLessons) {
			return in_array($sortedLesson->id, $completeLessons);
		});

		$sortedInProgressLessons = $sortedLessons->filter(function($sortedLesson) use ($completeLessons) {
			return !in_array($sortedLesson->id, $completeLessons);
		});

		$requiredInProgressLessons = $sortedInProgressLessons->filter(function($lesson) {
			return $lesson->is_required === 1;
		})->count();

		foreach ($sortedCompletedLessons as $lesson) {
			$lessonId = $lesson->id;
			DB::table('user_lesson')
				->where('lesson_id', $lessonId)
				->update(['start_date' => $presetStartDate]);
		};

		if ($workLoad) {
			if ($workLoad === 0) {
				foreach ($sortedLessons as $lesson) {
					$lessonId = $lesson->id;
					DB::table('user_lesson')
						->where('lesson_id', $lessonId)
						->update(['start_date' => Carbon::now()]);
				}
			} else {
				UserLessonApiController::insertPlan($sortedInProgressLessons, $workDays, $workLoad, $presetStartDate);
			}
		} else {
			UserLessonApiController::calculatePlan($requiredInProgressLessons, $sortedInProgressLessons, $startDate, $daysQuantity, $workDays);
		}
	}

	static function calculatePlan($requiredInProgressLessons, $sortedInProgressLessons, $startDate, $daysQuantity, $workDays)
	{
		$daysExcess = $daysQuantity%$requiredInProgressLessons;
		$workLoad = floor($daysQuantity/$requiredInProgressLessons);
		$lessonWithExtraDay = 0;

		foreach ($sortedInProgressLessons as $lesson) {
			$lessonId = $lesson->id;
			$queriedLesson = DB::table('user_lesson')->where('lesson_id', $lessonId);
			$groupId = $lesson->group_id;

			echo($lessonWithExtraDay).PHP_EOL;

			if ($groupId === 2 || $groupId === 3 || $groupId === 15) {
				$queriedLesson->update(['start_date' => Carbon::now()]);
			} else if ($lessonWithExtraDay < $daysExcess) {
				$excessWorkload = $workLoad + 1;
				$startDateVariable = $startDate->addHours($excessWorkload * 24);
				$dayOfWeekIso = $startDateVariable->dayOfWeekIso;
				$isStartDateVariableAvilable = in_array($dayOfWeekIso, $workDays);

				if ($isStartDateVariableAvilable) {
					$lessonWithExtraDay++;
					$queriedLesson->update(['start_date' => $startDateVariable]);
				} else {
					while (!$isStartDateVariableAvilable) {
						$startDateVariable = $startDate->addDays(1);
						$dayOfWeekIso = $startDateVariable->dayOfWeekIso;
						$isStartDateVariableAvilable = in_array($dayOfWeekIso, $workDays);
					}
					$lessonWithExtraDay++;
					$queriedLesson->update(['start_date' => $startDateVariable]);
				}
			} else {
				$startDateVariable = $startDate->addHours($workLoad * 24);
				$dayOfWeekIso = $startDateVariable->dayOfWeekIso;
				$isStartDateVariableAvilable = in_array($dayOfWeekIso, $workDays);

				if ($isStartDateVariableAvilable) {
					$queriedLesson->update(['start_date' => $startDateVariable]);
				} else {
					while (!$isStartDateVariableAvilable) {
						$startDateVariable = $startDate->addDays(1);
						$dayOfWeekIso = $startDateVariable->dayOfWeekIso;
						$isStartDateVariableAvilable = in_array($dayOfWeekIso, $workDays);
					}
					$queriedLesson->update(['start_date' => $startDateVariable]);
				}
			}
		}
	}

	static function insertPlan($sortedInProgressLessons, $workDays, $workLoad, $presetStartDate)
	{
		foreach ($sortedInProgressLessons as $lesson) {
			$lessonId = $lesson->id;
			$queriedLesson = DB::table('user_lesson')->where('lesson_id', $lessonId);
			$groupId = $lesson->group_id;

			if ($groupId === 2 || $groupId === 3 || $groupId === 15) {
				$queriedLesson->update(['start_date' => Carbon::now()]);
			} else {
				$startDateVariable = $presetStartDate->addHours($workLoad * 24);
				$dayOfWeekIso = $startDateVariable->dayOfWeekIso;
				$isStartDateVariableAvilable = in_array($dayOfWeekIso, $workDays);

				if ($isStartDateVariableAvilable) {
					$queriedLesson->update(['start_date' => $startDateVariable]);
				} else {
					while (!$isStartDateVariableAvilable) {
						$startDateVariable = $presetStartDate->addDays(1);
						$dayOfWeekIso = $startDateVariable->dayOfWeekIso;
						$isStartDateVariableAvilable = in_array($dayOfWeekIso, $workDays);
					}
					$queriedLesson->update(['start_date' => $startDateVariable]);
				}
			}
		}
	}
}
