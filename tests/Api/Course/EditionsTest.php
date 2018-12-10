<?php

namespace Tests\Api\Course;

use App\Models\Edition;
use App\Models\User;
use Tests\Api\ApiTestCase;


class EditionsTest extends ApiTestCase
{

	/** @test */
	public function get_edition_include_groups()
	{
		$user = User::find(1);
		$edition = Edition::find(1);

		$response = $this
			->actingAs($user)
			->json('GET', 'papi/v2/editions/1?include=groups');

		$response
			->assertStatus(200)
			->assertJson([
				'id'     => $edition->id,
				'groups' => [

				],
			]);
	}

}
