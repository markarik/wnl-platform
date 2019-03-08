<?php

namespace Tests\Api\Course;

use App\Models\Course;
use App\Models\CourseStructureNode;
use App\Models\Lesson;
use App\Models\LessonProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserLesson;
use App\Models\UserSubscription;
use \Facades\App\Contracts\CourseProvider;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\Api\ApiTestCase;


class CourseStructureNodesTest extends ApiTestCase
{
	public function testLessonsInVariousProducts() {
		//// PREPARE ////
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

		//// RUN ////
		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/course_structure_nodes/' . $course->id . '?include=lessons'));
		$responseLessons = $response->json('included.lessons');

		//// ASSERT ////
		$response->assertStatus(200);
		$this->assertCount(2, $responseLessons);

		// Lesson in latest paid course product
		$this->assertIsArray($responseLessons[$lessonInLatestPaidCourseProduct->id]);
		$this->assertEquals($mediumTimeAgo->timestamp, $responseLessons[$lessonInLatestPaidCourseProduct->id]['startDate']);
		$this->assertTrue($responseLessons[$lessonInLatestPaidCourseProduct->id]['isAccessible']);
		$this->assertTrue($responseLessons[$lessonInLatestPaidCourseProduct->id]['isAvailable']);

		// Lesson not in latest paid course product
		$this->assertIsArray($responseLessons[$lessonNotInLatestPaidCourseProduct->id]);
		$this->assertNull($responseLessons[$lessonNotInLatestPaidCourseProduct->id]['startDate']);
		$this->assertFalse($responseLessons[$lessonNotInLatestPaidCourseProduct->id]['isAccessible']);
		$this->assertFalse($responseLessons[$lessonNotInLatestPaidCourseProduct->id]['isAvailable']);
	}

	public function testLessonsWithCustomStartDates() {
		//// PREPARE ////
		$now = Carbon::now();
		$courseStartDate = $now->subDays(50);
		$defaultLessonStartDate = $now->subDays(40);
		$customLessonStartDate = $now->subDays(30);

		/** @var User $user */
		$user = factory(User::class)->create();
		factory(UserSubscription::class)->create([
			'user_id' => $user->id
		]);

		$product = $this->createProductWithOrder($user, $courseStartDate, true);

		$course = factory(Course::class)->create();
		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);

		/** @var Collection $lessons */
		$lessons = factory(Lesson::class, 2)->create();
		$lessons->each(function (Lesson $lesson, $index) use ($course, $product, $defaultLessonStartDate) {
			$this->addLessonToCourseStructure($course, $lesson);
			$this->addLessonToProduct($product, $lesson, $defaultLessonStartDate);
		});

		$lessonWithDefaultStartDate = $lessons->get(0);
		$lessonWithCustomStartDate = $lessons->get(1);

		factory(UserLesson::class)->create([
			'user_id' => $user->id,
			'lesson_id' => $lessonWithCustomStartDate->id,
			'start_date' => $customLessonStartDate
		]);

		//// RUN ////
		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/course_structure_nodes/' . $course->id . '?include=lessons'));
		$responseLessons = $response->json('included.lessons');

		//// ASSERT ////
		$response->assertStatus(200);
		$this->assertCount(2, $responseLessons);

		$this->assertIsArray($responseLessons[$lessonWithCustomStartDate->id]);
		$this->assertEquals($customLessonStartDate->timestamp, $responseLessons[$lessonWithCustomStartDate->id]['startDate']);
		$this->assertTrue($responseLessons[$lessonWithCustomStartDate->id]['isAccessible']);
		$this->assertTrue($responseLessons[$lessonWithCustomStartDate->id]['isAvailable']);
		$this->assertFalse($responseLessons[$lessonWithCustomStartDate->id]['isDefaultStartDate']);

		$this->assertIsArray($responseLessons[$lessonWithDefaultStartDate->id]);
		$this->assertEquals($defaultLessonStartDate->timestamp, $responseLessons[$lessonWithDefaultStartDate->id]['startDate']);
		$this->assertTrue($responseLessons[$lessonWithDefaultStartDate->id]['isAccessible']);
		$this->assertTrue($responseLessons[$lessonWithDefaultStartDate->id]['isAvailable']);
		$this->assertTrue($responseLessons[$lessonWithDefaultStartDate->id]['isDefaultStartDate']);
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
