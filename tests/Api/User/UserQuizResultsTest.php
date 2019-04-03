<?php

namespace Tests\Api\User;

use App\Jobs\CalculateExamResults;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\Api\ApiTestCase;
use Facades\App\Contracts\CourseProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserQuizResultsTest extends ApiTestCase
{
	use DatabaseTransactions;

	public function testEntryExamMarkedAsFinished()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		/** @var Course $course */
		$course = factory(Course::class)->create([
			'entry_exam_tag_id' => 1,
		]);

		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);
		Bus::fake();

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/quiz_results/$user->id"), [
				'results' => [],
				'meta' => [
					'examMode' => true,
					'examTagId' => 1,
					'allQuestionsSolved' => true,
				]
			]);

		$response->assertStatus(200);
		$userFromDb = User::find($user->id);
		$this->assertEquals(true, $userFromDb->has_finished_entry_exam);

		Bus::assertDispatched(CalculateExamResults::class);
	}

	public function testEntryExamNotMarkedAsFinishedWhenOtherQuizIsSolved()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		/** @var Course $course */
		$course = factory(Course::class)->create([
			'entry_exam_tag_id' => 1,
		]);

		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);
		Bus::fake();

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/quiz_results/$user->id"), [
				'results' => [],
				'meta' => [
					'examMode' => true,
					'examTagId' => 2,
					'allQuestionsSolved' => true,
				]
			]);

		$response->assertStatus(200);
		$userFromDb = User::find($user->id);
		$this->assertEquals(false, $userFromDb->has_finished_entry_exam);

		Bus::assertDispatched(CalculateExamResults::class);
	}

	public function testEntryExamNotMarkedAsFinishedWhenNotAllQuestionsSolved()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		/** @var Course $course */
		$course = factory(Course::class)->create([
			'entry_exam_tag_id' => 1,
		]);

		CourseProvider::shouldReceive('getCourseId')->andReturn($course->id);
		Bus::fake();

		$response = $this
			->actingAs($user)
			->json('POST', $this->url("/quiz_results/$user->id"), [
				'results' => [],
				'meta' => [
					'examMode' => true,
					'examTagId' => 1,
					'allQuestionsSolved' => false,
				]
			]);

		$response->assertStatus(200);

		$userFromDb = User::find($user->id);
		$this->assertEquals(false, $userFromDb->has_finished_entry_exam);

		Bus::assertDispatched(CalculateExamResults::class);
	}
}
