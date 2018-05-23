<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Jobs\CalculateCoursePlan;
use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
use Cache;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserLesson;
use App\Http\Requests\User\UpdateLessonsPreset;
use App\Http\Requests\User\UpdateLessonsBatch;
use App\Models\UserLesson;
use App\Models\User;
use DB;


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
		if (empty($userId)) {
			return $this->respondNotFound();
		}

		$options = [
			'startDate' => Carbon::parse($request->start_date)->timezone($request->timezone),
			'endDate'   => Carbon::parse($request->end_date)->timezone($request->timezone),
			'workLoad'  => $request->work_load,
			'workDays'  => $request->work_days,
			'preset'    => $request->preset_active,
		];

		$plan = dispatch_now(new CalculateCoursePlan($user, $options));

		Cache::tags("user-{$user->id}")->flush();
		$lessons = $user->lessonsAvailability()->get();
		$controller = new LessonsApiController($request);

		return $this->respondOk([
			'lessons'        => $controller->transform($lessons),
			'end_date'       => $plan->last()['start_date']->timestamp,
			'end_date_human' => $plan->last()['start_date'],
		]);
	}

	public function putBatch(UpdateLessonsBatch $request, $userId)
	{
		$userId = User::find($userId)->id;
		if (empty($userId)) {
			return $this->respondNotFound();
		}

		foreach ($request->manual_start_dates as $lesson) {
			if (empty($lesson['startDate'])) {
				return $this->respondUnprocessableEntity();
			}
			DB::table('user_lesson')
				->where('user_id', $userId)
				->where('lesson_id', $lesson['lessonId'])
				->update(['start_date' => Carbon::parse($lesson['startDate'])]);
		}

		Cache::tags("user-$userId")->flush();

		return $this->respondOk();
	}
}
