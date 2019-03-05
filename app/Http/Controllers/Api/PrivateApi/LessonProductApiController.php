<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\User\UpdateLessonsBatch;
use App\Http\Requests\User\UpdateLessonsPreset;
use App\Http\Requests\User\UpdateUserLesson;
use App\Jobs\CalculateCoursePlan;
use App\Models\LessonProduct;
use App\Models\User;
use App\Models\UserLesson;
use Carbon\Carbon;
use Illuminate\Http\Request;


class LessonProductApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.lesson-product');
	}

	public function getForProduct(Request $request, $productId) {
		$result = LessonProduct::where(['product_id' => $productId])->get();

		return $this->transformAndRespond($result);
	}

	public function put(UpdateUserLesson $request)
	{
		$productId = $request->productId;
		$lessonId = $request->lessonId;

		$userLesson = UserLesson::where([
			'lesson_id' => $lessonId,
			'user_id'   => $productId,
		])->first();


		if (!$userLesson) {
			return $this->respondNotFound();
		}

		$userLesson->update([
			'start_date' => Carbon::parse($request->input('date')),
		]);

		return $this->respondOk();
	}

	public function putPlan(UpdateLessonsPreset $request, $productId)
	{
		$user = User::find($productId);
		if (empty($productId)) {
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

		$lessons = $user->lessonsAvailability()->get();
		$controller = new LessonsApiController($request);

		return $this->respondOk([
			'lessons'        => $controller->transform($lessons),
			'end_date'       => $plan->last()['start_date']->timestamp ?? Carbon::now()->timestamp,
		]);
	}

	public function putBatch(UpdateLessonsBatch $request, $productId)
	{
		$productId = User::find($productId)->id;
		if (empty($productId)) {
			return $this->respondNotFound();
		}

		foreach ($request->manual_start_dates as $lesson) {
			if (empty($lesson['startDate'])) {
				return $this->respondUnprocessableEntity();
			}

			UserLesson::updateOrCreate(
				[
					'user_id' => $productId,
					'lesson_id' => $lesson['lessonId']
				],
				['start_date' => Carbon::parse($lesson['startDate'])]
			);
		}

		return $this->respondOk();
	}
}
