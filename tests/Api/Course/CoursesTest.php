<?php

namespace Tests\Api\Course;

use App\Models\Course;
use App\Models\User;
use Tests\Api\ApiTestCase;


class CoursesTest extends ApiTestCase
{

	/** @test */
	public function get_course_include_groups()
	{
		$user = User::find(1);
		$course = Course::find(1);

		$response = $this
			->actingAs($user)
			->json('GET', 'papi/v2/courses/1?include=groups');

		$response
			->assertStatus(200)
			->assertJson([
				'id'     => $course->id,
				'groups' => [

				],
			]);
	}

}
