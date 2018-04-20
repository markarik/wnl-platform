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

		$this
			->actingAs($user)
			->json('PUT', $this->url("/user_lesson/$user->id"), [
				'work_load' => 0,
				'start_date' => Carbon::now()->toDateString(),
				'user_id' => $user->id,
				'work_days' => ['1,2,5']
			]);

		foreach($user->lessonsAvailability as $lesson) {
			$this->assertTrue($lesson->startDate($user)->isToday(), "Start date is not today");
		};
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
		$endDate = (clone $startDate)->addDays(8);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/user_lesson/$user->id"), [
				'start_date' => $startDate->toDateString(),
				'end_date' => $endDate->toDateString(),
				'user_id' => $user->id,
				'work_days' => [1,2,3,7],
				'preset_active' => ['dateToDate'],
			]);

		$response->assertStatus(200);

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
	}
}
