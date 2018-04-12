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
		$workdays = $request->workdays;
		$startDate = Carbon::parse($request->start_date);
		$endDate = Carbon::parse($request->end_date);
		$userCourseProgress = UserCourseProgress::where('user_id', $profileId);
		$subscriptionDateTimestamp = $user->getSubscriptionDatesAttribute();
		$subscriptionEndDate = Carbon::createFromTimestamp($subscriptionDateTimestamp["max"]);
		// echo($startDate).PHP_EOL;
		// echo($endDate).PHP_EOL;
		$daysLeft = $startDate->diffInDays($endDate);
		$sortedLessons = $user->lessonsAvailability()
			->orderBy('group_id')
			->orderBy('order_number')
			->get();

		$completeLessons = (clone $userCourseProgress)
			->whereNull('section_id')
			->whereNull('screen_id')
			->where('status', 'complete')
			->get()
			->pluck('lesson_id')
			->toArray();

		$lessons = $sortedLessons->filter(function($sortedLesson) use ($completeLessons) {
			return !in_array($sortedLesson->id, $completeLessons);
		});

		UserLessonApiController::insertPlan($lessons, $workdays);
	}

	static function insertPlan($lessons, $workdays)
	{
		$startDate = Carbon::now();
		foreach ($lessons as $lesson) {

			$lessonId = $lesson->id;
			$queriedLesson = DB::table('user_lesson')->where('lesson_id', $lessonId);
			$groupId = $lesson->group_id;

			if ($groupId === 2 || $groupId === 3 || $groupId === 15) {
				$queriedLesson->update(['start_date' => Carbon::now()]);
			} else {
				$startDateVariable = $startDate->addHours($workdays * 24);

				if ($startDateVariable->isWeekend()) {
					$startDateVariable->next(Carbon::MONDAY);
				}

				$queriedLesson->update(['start_date' => $startDateVariable]);
			}
		}
	}
}
