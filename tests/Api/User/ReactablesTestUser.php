<?php

namespace Tests\Api\Qna;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class UserReactionsTest extends ApiTestCase
{

	use DatabaseTransactions;

	/** @test */
	public function search_user_reactions_by_reaction_name()
	{
		$user = User::find(1);

		$response = $this
			->actingAs($user)
			->json('GET', $this->url("/users/{$user->id}/reactions/bookmark"));

		$response
			->assertStatus(200);
	}
}
