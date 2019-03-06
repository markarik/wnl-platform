<?php

namespace Tests\Api\User;

use App\Models\Course;
use App\Models\CourseStructureNode;
use App\Models\LessonProduct;
use App\Models\Order;
use App\Models\User;
use App\Models\Lesson;
use App\Models\UserLesson;
use Facades\App\Contracts\CourseProvider;
use Tests\Api\ApiTestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserLessonTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function openAllLessons()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		/** @var Lesson[] $lessons */
		$lessons = factory(Lesson::class, 10)->create();

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 1,
		]);

		/** @var Course $course */
		$course = factory(Course::class)->create();

		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);

		foreach ($lessons as $lesson) {
			factory(CourseStructureNode::class)->create([
				'course_id' => $course->id,
				'structurable_type' => Lesson::class,
				'structurable_id' => $lesson->id
			]);

			factory(LessonProduct::class)->create([
				'product_id' => $order->product_id,
				'lesson_id' => $lesson->id,
				'start_date' => Carbon::now()->subDays(100)
			]);
		}

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/user_lesson/$user->id"), [
				'work_load' => 0,
				'start_date' => Carbon::now()->toDateString(),
				'user_id' => $user->id,
				'work_days' => [1,2,5],
				'timezone' => 'UTC',
				'preset_active' => 'openAll'
			]);

		$response->assertStatus(200);

		foreach ($user->getLessonsAvailability() as $lesson) {
			$this->assertTrue($lesson->startDate($user)->isToday(), "Start date is not today");
		};

		$endDate = $response->json('end_date');
		$this->assertTrue(Carbon::createFromTimestamp($endDate)->lte(Carbon::now()));

		$this->assertContains([
			'id'=> $lesson->id,
			'name' => $lesson->name,
			'group_id' => $lesson->group_id,
			'groups' => $lesson->group_id,
			'order_number' => $lesson->order_number,
			'is_required' => $lesson->is_required,
			'isAccessible' => $lesson->isAccessible(),
			'isAvailable' => $lesson->isAvailable(),
			'startDate' => $endDate,
		], $response->json('lessons'));
	}

	/** @test */
	public function insertDateToDatePlan()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 1,
		]);

		/** @var Course $course */
		$course = factory(Course::class)->create();

		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);

		/** @var Lesson[] $requiredLessons */
		$requiredLessons = factory(Lesson::class, 5)->create([
			'is_required' => 1,
		]);

		foreach ($requiredLessons as $lesson) {
			factory(CourseStructureNode::class)->create([
				'course_id' => $course->id,
				'structurable_type' => Lesson::class,
				'structurable_id' => $lesson->id
			]);

			factory(LessonProduct::class)->create([
				'product_id' => $order->product_id,
				'lesson_id' => $lesson->id,
				'start_date' => Carbon::now()->subDays(100)
			]);
		}

		$startDate = Carbon::parse('next monday');
		$endDate = (clone $startDate)->addDays(13);
		$expectedDaysInterval = [0, 2, 5, 2, 4];

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/user_lesson/$user->id"), [
				'start_date' => $startDate->toDateString(),
				'end_date' => $endDate->toDateString(),
				'user_id' => $user->id,
				'work_days' => [1,2,3,7],
				'preset_active' => 'dateToDate',
				'timezone' => 'UTC'
			]);

		$response->assertStatus(200);
		$responseLessons = $response->json('lessons');

		foreach ($requiredLessons as $index => $lesson) {
			$expectedStartDate = $startDate->addDays($expectedDaysInterval[$index]);
			$this->assertDatabaseHas('user_lesson', [
				'user_id' => $user->id,
				'lesson_id' => $lesson->id,
				'start_date' => $expectedStartDate,
			]);

			$this->assertContains([
				'id'=> $lesson->id,
				'name' => $lesson->name,
				'group_id' => $lesson->group_id,
				'groups' => $lesson->group_id,
				'order_number' => $lesson->order_number,
				'is_required' => $lesson->is_required,
				'isAccessible' => $lesson->isAccessible(),
				'isAvailable' => $lesson->isAvailable(),
				// FIXME it fails because all the start dates are the same
				 'startDate' => $expectedStartDate->timestamp
			], $responseLessons);
		}

		$computedEndDate = $response->json('end_date');
		$this->assertTrue(
			Carbon::createFromTimestamp($computedEndDate)->lte($endDate),
			"Computed End Date is larger than selected end date"
		);
	}

	/** @test */
	public function insertPreset()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 1,
		]);

		/** @var Course $course */
		$course = factory(Course::class)->create();

		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);

		/** @var Lesson[] $requiredLessons */
		$requiredLessons = factory(Lesson::class, 5)->create([
			'is_required' => 1,
		]);

		foreach ($requiredLessons as $lesson) {
			factory(CourseStructureNode::class)->create([
				'course_id' => $course->id,
				'structurable_type' => Lesson::class,
				'structurable_id' => $lesson->id
			]);

			factory(LessonProduct::class)->create([
				'product_id' => $order->product_id,
				'lesson_id' => $lesson->id,
				'start_date' => Carbon::now()->subDays(100)
			]);
		}

		$startDate = Carbon::parse('next monday');

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/user_lesson/$user->id"), [
				'work_load' => 2,
				'start_date' => $startDate->toDateTimeString(),
				'user_id' => $user->id,
				'work_days' => [1,2,5,6,7],
				'timezone' => 'UTC',
				'preset_active' => 'daysPerLesson'
			]);

		$response->assertStatus(200);
		$responseLessons = $response->json('lessons');
		$expectedDaysInterval = [0, 4, 2, 2, 4];

		foreach ($requiredLessons as $index => $lesson) {
			$expectedStartDate = $startDate->addDays($expectedDaysInterval[$index]);

			$this->assertDatabaseHas('user_lesson', [
				'user_id' => $user->id,
				'lesson_id' => $lesson->id,
				'start_date' => $expectedStartDate,
			]);

			$this->assertContains([
				'id'=> $lesson->id,
				'name' => $lesson->name,
				'group_id' => $lesson->group_id,
				'groups' => $lesson->group_id,
				'order_number' => $lesson->order_number,
				'is_required' => $lesson->is_required,
				'isAccessible' => $lesson->isAccessible(),
				'isAvailable' => $lesson->isAvailable(),
				// FIXME it fails because all the start dates are the same
				 'startDate' => $expectedStartDate->timestamp
			], $responseLessons);
		}
	}

	/** @test */
	public function manuallyChangeDates()
	{
		$user = factory(User::class)->create();

		$requiredLessonOne = factory(Lesson::class)->create([
			'is_required' => 1,
			'group_id' => 5,
			'order_number' => 1,
		]);

		$requiredLessonTwo = factory(Lesson::class)->create([
			'is_required' => 1,
			'group_id' => 5,
			'order_number' => 2,
		]);

		$userLessonOne = factory(UserLesson::class)->create([
			'user_id' => $user->id,
			'lesson_id' => $requiredLessonOne->id,
			'start_date' => Carbon::now()->subDays(100)
		]);

		$userLessonOne = factory(UserLesson::class)->create([
			'user_id' => $user->id,
			'lesson_id' => $requiredLessonTwo->id,
			'start_date' => Carbon::now()->subDays(100)
		]);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/user_lesson/$user->id/batch"), [
				'manual_start_dates' => [
					[
						'lessonId' => $requiredLessonOne->id,
						'startDate' => '2018-06-05T22:00:00.000Z',
					],
					[
						'lessonId' => $requiredLessonTwo->id,
						'startDate' => '2018-05-30T22:00:00.000Z',
					]
				],
				'timezone' => 'UTC',
			]);

			$this->assertDatabaseHas('user_lesson', [
				'user_id' => $user->id,
				'lesson_id' => $requiredLessonOne->id,
				'start_date' => '2018-06-05T22:00:00.000Z',
			]);

			$this->assertDatabaseHas('user_lesson', [
				'user_id' => $user->id,
				'lesson_id' => $requiredLessonTwo->id,
				'start_date' => '2018-05-30T22:00:00.000Z',
			]);

		$response->assertStatus(200);
	}
}
