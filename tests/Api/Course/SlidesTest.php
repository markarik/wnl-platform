<?php

namespace Tests\Api\Course;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class SlidesTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function post_slide()
	{
		$user = User::find(1);

		$data = [
			'content'       => '<section>siema</section>',
			'is_functional' => false,
			'order_number'  => 20,
			'screen'        => 5,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/slides'), $data);

		$response
			->assertStatus(200);
	}

}
