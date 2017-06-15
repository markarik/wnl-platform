<?php

namespace Tests\Api\Qna;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class ReactionsTest extends ApiTestCase
{

	use DatabaseTransactions;

	/** @test */
	public function post_reaction_with_context()
	{
		$user = User::find(1);

		$data = [
			'reactable_resource' => config('papi.resources.answers'),
			'reactable_id'       => 2,
			'reaction_type'      => 'thanks',
			'context'            => '{"very": "cool", "json": {"data": "or", "whatever": true}}',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/reactions'), $data);

		$response
			->assertStatus(201);
	}

	/** @test */
	public function post_reaction_to_slide()
	{
		$user = User::find(1);

		$data = [
			'reactable_resource' => config('papi.resources.slides'),
			'reactable_id'       => 100,
			'reaction_type'      => 'bookmark',
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
