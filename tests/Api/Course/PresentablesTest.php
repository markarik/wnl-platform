<?php

namespace Tests\Api\Course;

use App\Models\User;
use Tests\Api\ApiTestCase;


class PresentablesTest extends ApiTestCase
{

	/** @test */
	public function search_presentables()
	{
		$user = User::find(1);
		$data = [
			'query' => [
				'where' => [
					['presentable_type', 'App\Models\Slideshow'],
					['presentable_id', '=', 1],
				],
			],
			'join'  => [
				['slides', 'presentables.slide_id', '=', 'slides.id'],
			],
			'order' => [
				'order_number' => 'asc',
			],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/presentables/.search'), $data);

		$response
			->assertStatus(200);

	}

}
