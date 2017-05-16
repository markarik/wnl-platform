<?php

namespace Tests\Api\Course;

use App\Models\User;
use Tests\Api\ApiTestCase;


class SlideshowsTest extends ApiTestCase
{

	/** @test */
	public function get_slideshows()
	{
		$user = User::find(1);

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/slideshows/all'));

		$response
			->assertStatus(200);
	}

}
