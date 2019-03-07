<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Product\CreateLessonProduct;
use App\Http\Requests\Product\UpdateLessonProduct;
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

	public function getForProduct(Request $request, $productId) {
		$result = LessonProduct::where(['product_id' => $productId])->get();

		return $this->transformAndRespond($result);
	}

	public function put(UpdateLessonProduct $request, $productId, $lessonId)
	{
		$lessonProduct = LessonProduct::where([
			'lesson_id' => $lessonId,
			'product_id' => $productId,
		])->first();

		if (empty($lessonProduct)) {
			return $this->respondNotFound();
		}

		$lessonProduct['start_date'] = Carbon::createFromTimestamp($request['start_date']);

		return $this->transformAndRespond($lessonProduct);
	}

	public function post(CreateLessonProduct $request, $productId)
	{
		$product = Product::find($productId);
		if (empty($product)) {
			return $this->respondNotFound();
		}

		$lessonProduct = LessonProduct::create([
			'lesson_id' => $request['lesson_id'],
			'product_id' => $product->id,
			'start_date' => Carbon::createFromTimestamp($request['start_date'])
		]);

		return $this->transformAndRespond($lessonProduct);
	}

	public function deleteLesson($productId, $lessonId) {
		$product = Product::find($productId);
		if (empty($product)) {
			return $this->respondNotFound();
		}

		if (!$product->lessons->contains($lessonId)) {
			return $this->respondNotFound();
		}

		LessonProduct::where([
			'lesson_id' => $lessonId,
			'product_id' => $product->id
		])->delete();

		return $this->respondOk();
	}
}
