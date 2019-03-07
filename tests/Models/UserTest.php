<?php

namespace Tests\Models;

use App\Models\Product;
use \Facades\App\Contracts\CourseProvider;
use App\Models\Course;
use App\Models\CourseStructureNode;
use App\Models\Lesson;
use App\Models\LessonProduct;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserTest extends TestCase
{
	static $LONG_TIME_AGO = 100;
	static $MEDIUM_TIME_AGO = 50;
	static $SHORT_TIME_AGO = 10;

	public function testGetLatestPaidCourseProductId() {
		$user = factory(User::class)->create();

		//// Prepare products and orders ////
		$oldPaidCourseProduct = factory(Product::class)->create([
			'course_start' => Carbon::now()->subDays(static::$LONG_TIME_AGO)
		]);

		factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 1,
			'product_id' => $oldPaidCourseProduct->id
		]);

		$latestPaidCourseProduct = factory(Product::class)->create([
			'course_start' => Carbon::now()->subDays(static::$MEDIUM_TIME_AGO)
		]);

		factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 1,
			'product_id' => $latestPaidCourseProduct->id
		]);

		$notPaidCourseProduct = factory(Product::class)->create([
			'course_start' => Carbon::now()->subDays(static::$SHORT_TIME_AGO)
		]);

		factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 0,
			'product_id' => $notPaidCourseProduct->id
		]);

		$latestPaidOtherProduct = factory(Product::class)->create([
			'course_start' => Carbon::now()->subDays(static::$SHORT_TIME_AGO)
		]);

		factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 1,
			'product_id' => $latestPaidOtherProduct->id
		]);

		//// Prepare course structure and lesson product////
		$course = factory(Course::class)->create();

		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);

		$lessons = factory(Lesson::class, 5)->create();

		foreach ($lessons as $lesson) {
			factory(CourseStructureNode::class)->create([
				'course_id' => $course->id,
				'structurable_type' => Lesson::class,
				'structurable_id' => $lesson->id
			]);

			factory(LessonProduct::class)->create([
				'product_id' => $oldPaidCourseProduct->id,
				'lesson_id' => $lesson->id,
				'start_date' => Carbon::now()->subDays(static::$LONG_TIME_AGO)
			]);

			factory(LessonProduct::class)->create([
				'product_id' => $latestPaidCourseProduct->id,
				'lesson_id' => $lesson->id,
				'start_date' => Carbon::now()->subDays(static::$MEDIUM_TIME_AGO)
			]);

			factory(LessonProduct::class)->create([
				'product_id' => $notPaidCourseProduct->id,
				'lesson_id' => $lesson->id,
				'start_date' => Carbon::now()->subDays(static::$SHORT_TIME_AGO)
			]);
		}

		//// Assert ////
		$this->assertEquals($latestPaidCourseProduct->id, $user->getLatestPaidCourseProductId());
	}
}
