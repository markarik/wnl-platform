<?php

namespace Tests\Api\Course;

use App\Models\Course;
use App\Models\CourseStructureNode;
use App\Models\Lesson;
use App\Models\LessonProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserSubscription;
use \Facades\App\Contracts\CourseProvider;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\Api\ApiTestCase;


class CourseStructureNodesTest extends ApiTestCase
{
	public function testLessonsInVariousProducts() {
		$longTimeAgo = Carbon::now()->subDays(100);
		$mediumTimeAgo = Carbon::now()->subDays(50);
		$shortTimeAgo = Carbon::now()->subDays(10);

		/** @var User $user */
		$user = factory(User::class)->create();
		factory(UserSubscription::class)->create([
			'user_id' => $user->id
		]);

		$oldPaidCourseProduct = $this->createProductWithOrder($user, $longTimeAgo, true);
		$latestPaidCourseProduct = $this->createProductWithOrder($user, $mediumTimeAgo, true);
		$notPaidCourseProduct = $this->createProductWithOrder($user, $shortTimeAgo, false);
		/*$latestPaidOtherProduct = */$this->createProductWithOrder($user, $shortTimeAgo, true);

		$course = factory(Course::class)->create();
		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);

		/** @var Collection $lessons */
		$lessons = factory(Lesson::class, 2)->create();

		$lessonInLatestPaidCourseProduct = $lessons->get(0);
		$this->addLessonToCourseStructure($course, $lessonInLatestPaidCourseProduct);
		$this->addLessonToProduct($oldPaidCourseProduct, $lessonInLatestPaidCourseProduct, $longTimeAgo);
		$this->addLessonToProduct($latestPaidCourseProduct, $lessonInLatestPaidCourseProduct, $mediumTimeAgo);
		$this->addLessonToProduct($notPaidCourseProduct, $lessonInLatestPaidCourseProduct, $shortTimeAgo);

		$lessonNotInLatestPaidCourseProduct = $lessons->get(1);
		$this->addLessonToCourseStructure($course, $lessonNotInLatestPaidCourseProduct);
		$this->addLessonToProduct($oldPaidCourseProduct, $lessonNotInLatestPaidCourseProduct, $longTimeAgo);
		$this->addLessonToProduct($notPaidCourseProduct, $lessonNotInLatestPaidCourseProduct, $shortTimeAgo);

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/course_structure_nodes/' . $course->id . '?include=lessons'));

		$response->assertStatus(200);
		$responseLessons = $response->json('included.lessons');

		$this->assertCount(2, $responseLessons);

		// Lesson in latest paid course product
		$this->assertIsArray($responseLessons[$lessonInLatestPaidCourseProduct->id], 'lesson is not on the list');
		$this->assertEquals(
			$mediumTimeAgo->timestamp,
			$responseLessons[$lessonInLatestPaidCourseProduct->id]['startDate'],
			'startDate is incorrect'
		);
		$this->assertTrue($responseLessons[$lessonInLatestPaidCourseProduct->id]['isAccessible'], 'lesson is not accessible when it should');
		$this->assertTrue($responseLessons[$lessonInLatestPaidCourseProduct->id]['isAvailable'], 'lesson is not available when it should');

		// Lesson not in latest paid course product
		$this->assertIsArray($responseLessons[$lessonNotInLatestPaidCourseProduct->id], 'lesson is not on the list');
		$this->assertNull(
			$responseLessons[$lessonNotInLatestPaidCourseProduct->id]['startDate'],
			'startDate should be null'
		);
		$this->assertFalse($responseLessons[$lessonNotInLatestPaidCourseProduct->id]['isAccessible'], 'lesson is accessible when it should not');
		$this->assertFalse($responseLessons[$lessonNotInLatestPaidCourseProduct->id]['isAvailable'], 'lesson is available when it should not');
	}

	private function createProductWithOrder(User $user, Carbon $courseStart, bool $paid)
	{
		/** @var Product $product */
		$product = factory(Product::class)->create([
			'course_start' => $courseStart
		]);

		factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => $paid,
			'product_id' => $product->id
		]);

		return $product;
	}

	private function addLessonToCourseStructure(Course $course, Lesson $lesson)
	{
		factory(CourseStructureNode::class)->create([
			'course_id' => $course->id,
			'structurable_type' => Lesson::class,
			'structurable_id' => $lesson->id
		]);
	}

	private function addLessonToProduct(Product $product, Lesson $lesson, Carbon $startDate)
	{
		factory(LessonProduct::class)->create([
			'product_id' => $product->id,
			'lesson_id' => $lesson->id,
			'start_date' => $startDate
		]);
	}
}
