<?php namespace App\Http\Controllers\Api\PrivateApi;

use DB;
use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
use Cache;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use App\Http\Requests\User\UpdateUserLesson;
use App\Http\Requests\User\UpdateLessonsPreset;
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
		$endDate = $request->end_date;
		$daysPerLesson = $request->days_per_lesson;
		$user = User::find($userId);

		$daysLeft = Carbon::now()->diffInDays($endDate);

		$lessons = $user->lessonsAvailability()->orderBy('order_number')->get();

		echo($lessons->count()).PHP_EOL;

		UserLessonApiController::insertPlan($lessons, $daysPerLesson);
	}
	static function insertPlan($lessons, $workdays)
	{
		$startDate = Carbon::now();

		foreach ($lessons as $key => $lesson) {
			$lessonId = $lesson->id;
			$queriedLesson = DB::table('user_lesson')->where('lesson_id', $lessonId);
			if ($lesson->order_number == 0) {
				// kejs, gdzie jest order_number=1 trzeba lepiej obsłużyć, n. 0||1 - acz to rozwiązanie nie działa :( stąd ponowne sprawdzenie - to be done
				$queriedLesson->update(['start_date' => $startDate]);
			} else if ($lesson->order_number == 1) {
				$queriedLesson->update(['start_date' => $startDate]);
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
