<?php

namespace Tests\Api\User;

use App\Models\User;
use App\Models\Lesson;
use App\Models\UserLesson;
use Tests\Api\ApiTestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserLessonTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function openAllLessons()
	{
		$user = factory(User::class)->create();
		$lessons = factory(Lesson::class, 10)->create();

		foreach ($lessons as $lesson) {
			factory(UserLesson::class)->create([
				'user_id' => $user->id,
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
				'work_days' => [1,2,5]
			]);

		foreach($user->lessonsAvailability as $lesson) {
			$this->assertTrue($lesson->startDate($user)->isToday(), "Start date is not today");
		};

		$endDate = $response->json()['end_date'];
		$this->assertTrue(Carbon::createFromTimestamp($endDate)->lte(Carbon::now()));

		$response
			->assertStatus(200)
			->assertJsonFragment([
						[
							'id'=> $lesson->id,
							'name' => $lesson->name,
							'group_id' => $lesson->group_id,
							'groups' => $lesson->group_id,
							'order_number' => $lesson->order_number,
							'editions' => $lesson->editions,
							'is_required' => $lesson->is_required,
							'isAccessible' => $lesson->isAccessible(),
							'isAvailable' => $lesson->isAvailable(),
							'startDate' => $endDate,
						]
			]);
	}

	/** @test */
	public function insertDateToDatePlan()
	{
		$user = factory(User::class)->create();
		$requiredLessons = [];

		for ($i = 1; $i < 6; $i++) {
			$requiredLessons[] = factory(Lesson::class)->create([
				'is_required' => 1,
				'group_id' => 5,
				'order_number' => $i,
			]);
		}

		foreach ($requiredLessons as $lesson) {
			factory(UserLesson::class)->create([
				'user_id' => $user->id,
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
			]);

		foreach ($requiredLessons as $index => $lesson) {
			$expectedStartDate = $startDate->addDays($expectedDaysInterval[$index]);
			$this->assertDatabaseHas('user_lesson', [
				'user_id' => $user->id,
				'lesson_id' => $lesson->id,
				'start_date' => $expectedStartDate,
			]);

			$response->assertJsonFragment([
				'id'=> $lesson->id,
				'name' => $lesson->name,
				'group_id' => $lesson->group_id,
				'groups' => $lesson->group_id,
				'order_number' => $lesson->order_number,
				'editions' => $lesson->editions,
				'is_required' => $lesson->is_required,
				'isAccessible' => $lesson->isAccessible(),
				'isAvailable' => $lesson->isAvailable(),
				'startDate' => $expectedStartDate->timestamp
			]);
		}

		$computedEndDate = $response->json()['end_date'];
		$this->assertTrue(
			Carbon::createFromTimestamp($computedEndDate)->lte($endDate),
			"Computed End Date is larger than selected end date"
		);


		$response
			->assertStatus(200);
	}

	/** @test */
	public function insertPreset()
	{
		$user = factory(User::class)->create();
		$requiredLessons = [];
		$userLessons = [];

		for ($i = 1; $i < 6; $i++) {
			$requiredLessons[] = factory(Lesson::class)->create([
				'is_required' => 1,
				'group_id' => 5,
				'order_number' => $i,
			]);
		}

		foreach ($requiredLessons as $lesson) {
			$userLessons[] = factory(UserLesson::class)->create([
				'user_id' => $user->id,
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
				'work_days' => [1,2,5,6,7]
			]);

		$expectedDaysInterval = [0, 4, 2, 2, 4];

		foreach ($requiredLessons as $index => $lesson) {
			$expectedStartDate = $startDate->addDays($expectedDaysInterval[$index]);

			$this->assertDatabaseHas('user_lesson', [
				'user_id' => $user->id,
				'lesson_id' => $lesson->id,
				'start_date' => $expectedStartDate,
			]);

			$response->assertJsonFragment([
				'id'=> $lesson->id,
				'name' => $lesson->name,
				'group_id' => $lesson->group_id,
				'groups' => $lesson->group_id,
				'order_number' => $lesson->order_number,
				'editions' => $lesson->editions,
				'is_required' => $lesson->is_required,
				'isAccessible' => $lesson->isAccessible(),
				'isAvailable' => $lesson->isAvailable(),
				'startDate' => $expectedStartDate->timestamp
			]);
		}

		$response->assertStatus(200);
	}
}
