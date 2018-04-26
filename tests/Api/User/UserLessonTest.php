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

		$response
			->assertStatus(200)
			->assertJson([
				'lessons' => [
					// foreach($user->lessonsAvailability as $lesson) {
						[
							'id'=> $lesson->id,
							'name' => $lesson->name,
							'group_id' => $lesson->group_id,
							'groups' => $lesson->groups,
							'order_number' => $lesson->order_number,
							'editions' => $lesson->editions,
							'is_required' => $lesson->is_required,
							'isAccessible' => $lesson->isAccessible(),
							'isAvailable' => $lesson->isAvailable(),
							'startDate' => Carbon::now()->timestamp,
						]
					// }
				],
				'end_date' => Carbon::now()->timestamp,
			]);
	}

	/** @test */
	public function insertDateToDatePlan()
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
			$userLessons = factory(UserLesson::class)->create([
				'user_id' => $user->id,
				'lesson_id' => $lesson->id,
				'start_date' => Carbon::now()->subDays(100)
			]);
		}

		$startDate = Carbon::parse('next monday');
		$endDate = (clone $startDate)->addDays(8);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/user_lesson/$user->id"), [
				'start_date' => $startDate->toDateString(),
				'end_date' => $endDate->toDateString(),
				'user_id' => $user->id,
				'work_days' => [1,2,3,7],
				'preset_active' => 'dateToDate',
			]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[0]->id,
			'start_date' => $startDate->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[1]->id,
			'start_date' => $startDate->addDays(2)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[2]->id,
			'start_date' => $startDate->addDays(4)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[3]->id,
			'start_date' => $startDate->addDays(2)->toDateTimeString(),
		]);

        $this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[4]->id,
			'start_date' => $startDate->addDays(1)->toDateTimeString(),
		]);

		$response
			->assertStatus(200)
			->assertJson([
				'lessons' => [
					[
						// 'id'=> $lesson->id,
						// 'name' => $lesson->name,
						// 'group_id' => $lesson->group_id,
						// 'groups' => $lesson->groups,
						// 'order_number' => $lesson->order_number,
						// 'editions' => $lesson->editions,
						// 'is_required' => $lesson->is_required,
						// 'isAccessible' => $lesson->isAccessible(),
						// 'isAvailable' => $lesson->isAvailable(),
						// 'startDate' => Carbon::now()->timestamp,
					]
				],
				'end_date' => Carbon::parse('next monday')->addDays(9)->timestamp,
			]);
	}

	/** @test */
	public function insertPreset()
	{
		$user = factory(User::class)->create();
		$requiredLessons = [];
		$userLessons = [];

		for ($i = 1; $i < 11; $i++) {
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

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[0]->id,
			'start_date' => $startDate->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[1]->id,
			'start_date' => $startDate->addDays(4)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[2]->id,
			'start_date' => $startDate->addDays(2)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[3]->id,
			'start_date' => $startDate->addDays(2)->toDateTimeString(),
		]);

        $this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[4]->id,
			'start_date' => $startDate->addDays(3)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[5]->id,
			'start_date' => $startDate->addDays(2)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[6]->id,
			'start_date' => $startDate->addDays(2)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[7]->id,
			'start_date' => $startDate->addDays(3)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[8]->id,
			'start_date' => $startDate->addDays(2)->toDateTimeString(),
		]);

		$this->assertDatabaseHas('user_lesson', [
			'user_id' => $user->id,
			'lesson_id' => $requiredLessons[9]->id,
			'start_date' => $startDate->addDays(2)->toDateTimeString(),
		]);

		$response
			->assertStatus(200)
			->assertJson([
				'lessons' => [
					[
						// 'id'=> $lesson->id,
						// 'name' => $lesson->name,
						// 'group_id' => $lesson->group_id,
						// 'groups' => $lesson->groups,
						// 'order_number' => $lesson->order_number,
						// 'editions' => $lesson->editions,
						// 'is_required' => $lesson->is_required,
						// 'isAccessible' => $lesson->isAccessible(),
						// 'isAvailable' => $lesson->isAvailable(),
						// 'startDate' => Carbon::now()->timestamp,
					]
				],
				'end_date' => Carbon::parse('next monday')->addDays(22)->timestamp,
			]);
	}
}
