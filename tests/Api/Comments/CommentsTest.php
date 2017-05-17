<?php

namespace Tests\Api\Comments;

use App\Models\User;
use Tests\Api\ApiTestCase;


class CommentsTest extends ApiTestCase
{

	/** @test */
	public function post_comment()
	{
		$user = User::find(1);

		$data = [
			'commentable_resource' => config('papi.resources.answers'),
			'commentable_id'       => 1,
			'text'                 => 'Kolekcjonuję antarktyczne drewniane kaczki, gdyby ktoś coś miał, proszę o info na priv. Pozdrawiam.',
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/comments'), $data);

		$response
			->assertStatus(200)
			->assertJsonStructure(['id', 'text', 'created_at', 'updated_at']);

	}

}
