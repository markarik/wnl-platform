<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
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

	public function putBatch(UpdateLessonProduct $request, $productId)
	{
		$product = Product::find($productId);
		if (empty($productId)) {
			return $this->respondNotFound();
		}

		foreach ($request->lessons as $lesson) {
			LessonProduct::updateOrCreate([
				'lesson_id' => $lesson['lesson_id'],
				'product_id' => $product->id
			], ['start_date' => Carbon::createFromTimestamp($lesson['start_date'])]);
		}

		return $this->respondOk();
	}

	public function deleteLesson($productId, $lessonId) {
		$product = Product::find($productId);
		if (empty($productId)) {
			return $this->respondNotFound();
		}

		if (!$product->lessons->contains($lessonId)) {
			return $this->respondNotFound();
		}

		$product->lessons()->detach($lessonId);

		return $this->respondOk();
	}
}
