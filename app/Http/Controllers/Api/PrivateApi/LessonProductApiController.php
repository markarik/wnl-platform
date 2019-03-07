<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Product\CreateLessonProduct;
use App\Http\Requests\Product\UpdateLessonProduct;
use App\Models\Lesson;
use App\Models\LessonProduct;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;


class LessonProductApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.lesson-product');
	}

	public function query(Request $request) {
		$productId = $request['product_id'];

		$result = LessonProduct::where(['product_id' => $productId])->get();

		return $this->transformAndRespond($result);
	}

	public function put(UpdateLessonProduct $request, LessonProduct $lessonProduct)
	{
		$lessonProduct['start_date'] = Carbon::createFromTimestamp($request['start_date']);
		$lessonProduct->save();

		return $this->transformAndRespond($lessonProduct);
	}

	public function post(CreateLessonProduct $request)
	{
		$lessonProduct = LessonProduct::create([
			'lesson_id' => $request['lesson_id'],
			'product_id' => $request['product_id'],
			'start_date' => Carbon::createFromTimestamp($request['start_date'])
		]);

		return $this->transformAndRespond($lessonProduct);
	}

	/**
	 * @param LessonProduct $lessonProduct
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function deleteLesson(LessonProduct $lessonProduct) {
		$lessonProduct->delete();

		return $this->respondOk();
	}
}
