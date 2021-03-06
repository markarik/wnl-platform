<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Jobs\CalculateCoursePlan;
use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
use Cache;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\User\UpdateUserLesson;
use App\Http\Requests\User\UpdateLessonsPreset;
use App\Http\Requests\User\UpdateLessonsBatch;
use App\Models\UserLesson;
use App\Models\User;
use DB;
use Symfony\Component\HttpFoundation\StreamedResponse;


class UserLessonApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-lesson');
	}

	public function getForUser(Request $request, $userId) {
		$result = UserLesson::where(['user_id' => $userId])->get();

		return $this->transformAndRespond($result);
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

		$lessons = $user->getLessonsAvailability();
		$controller = new LessonsApiController($request);

		return $this->respondOk([
			'lessons'        => $controller->transform($lessons),
			'end_date'       => $plan->last()['start_date']->timestamp ?? Carbon::now()->timestamp,
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

			UserLesson::updateOrCreate(
				[
					'user_id' => $userId,
					'lesson_id' => $lesson['lessonId']
				],
				['start_date' => Carbon::parse($lesson['startDate'])]
			);
		}

		return $this->respondOk();
	}

	public function exportPlan(Request $request)
	{
		$userId = $request->route('userId');
		$user = User::fetch($userId);

		if (empty($user)) {
			return $this->respondNotFound();
		}

		if (!Auth::user()->can('view', $user)) {
			return $this->respondForbidden();
		}

		$csvData = $user->getLessonsAvailability()
			->filter(function($userLesson) {
				return $userLesson->is_required;
			})
			->sortBy(function($userLesson) {
				return $userLesson->getStartDate();
			})
			->map(function($userLesson) {
				return [
					"name" => $userLesson->name,
					"start_date" => $userLesson->getStartDate()->setTimezone('Europe/Warsaw')->format('m/d/Y')
				];
			});

		$csvData->prepend([
			'name' => 'Subject',
			'start_date' => 'Start Date'
		]);

		return new StreamedResponse(
			function() use($csvData) {
				$buffer = fopen('php://output', 'w');
				foreach($csvData as $row) {
					fputcsv($buffer, $row);
				}
				fclose($buffer);
			},
			200,
			[
				'Content-type'        => 'text/csv',
				'Content-Disposition' => 'attachment; filename=plan_pracy.csv'
			]
		);
	}
}
