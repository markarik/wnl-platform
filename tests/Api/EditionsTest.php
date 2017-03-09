<?php

namespace Tests\Api;

use App\Models\Edition;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EditionsTest extends TestCase
{

	/** @test */
	public function api_returns_edition_structure()
	{
		$user = User::find(1);
		$edition = Edition::find(1);
		$course = $edition->course;
//		$groups = $course->groups()->with('lessons')->get();
//		$screens = $lesson->snippets()->with('slides')->get();

		$response = $this
			->actingAs($user)
			->json('GET', 'papi/v1/editions/1/structure');

		$response
			->assertStatus(200)
			->assertJson([
				'edition' => [
					'id'     => $edition->id,
					'groups' => [
						[

						],
					],
				],
			]);
	}

}
