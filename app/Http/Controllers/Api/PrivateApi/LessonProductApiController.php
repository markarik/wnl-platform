<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Product\UpdateLessonProduct;
use App\Http\Requests\User\UpdateLessonsBatch;
use App\Http\Requests\User\UpdateLessonsPreset;
use App\Http\Requests\User\UpdateUserLesson;
use App\Jobs\CalculateCoursePlan;
use App\Models\LessonProduct;
use App\Models\Product;
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

	public function putBatch(UpdateLessonProduct $request, $productId)
	{
		$product = Product::find($productId);
		if (empty($productId)) {
			return $this->respondNotFound();
		}

		foreach ($request->lessons as $lesson) {
			if ($product->lessons->contains($lesson['lesson_id'])) {
				$product->lessons()->updateExistingPivot(
					$lesson['lesson_id'], ['start_date' => Carbon::createFromTimestampUTC($lesson['start_date'])]
				);
			} else {
				$lessonModel = Lesson::find($lesson['lesson_id']);
				$product->lessons()->save($lessonModel, ['start_date' => Carbon::createFromTimestampUTC($lesson['start_date'])]);
			}
		}

		return $this->respondOk();
	}
}
