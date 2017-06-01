<?php

namespace Tests\Api\Qna;

use App\Models\User;
use Tests\Api\ApiTestCase;


class ReactionsTest extends ApiTestCase
{

	/** @test */
	public function post_reaction()
	{
		$user = User::find(1);

		$data = [
			'reactable_resource' => config('papi.resources.answers'),
			'reactable_id'       => 2,
			'reaction_type'      => 'thanks',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/reactions'), $data);

		$response
			->assertStatus(201);
	}

	/** @test * */
	public function delete_reaction()
	{
		$user = User::find(1);

		$data = [
			'reactable_resource' => config('papi.resources.answers'),
			'reactable_id'       => 1,
			'reaction_type'      => 'upvote',
		];

		$response = $this
			->actingAs($user)
			->json('DELETE', $this->url('/reactions'), $data);

		$response
			->assertStatus(200);
	}
}
