<?php

namespace Tests\Api\Qna;

use App\Models\QnaAnswer;
use App\Models\Slide;
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
		$qnaAnswer = factory(QnaAnswer::class)->create();

		$data = [
			[
				'reactable_resource' => 'qna_answer',
				'reactable_id'       => $qnaAnswer->id,
				'reaction_type'      => 'thanks',
				'context'            => '{"very": "cool", "json": {"data": "or", "whatever": true}}',
			]
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
		$slide = factory(Slide::class)->create();

		$data = [
			[
				'reactable_resource' => config('papi.resources.slides'),
				'reactable_id'       => $slide->id,
				'reaction_type'      => 'bookmark',
			]
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
		$qnaAnswer = factory(QnaAnswer::class)->create();

		$data = [
			'reactable_resource' => config('papi.resources.qna-answers'),
			'reactable_id'       => $qnaAnswer->id,
			'reaction_type'      => 'upvote',
		];

		$response = $this
			->actingAs($user)
			->json('DELETE', $this->url('/reactions'), $data);

		$response
			->assertStatus(200);
	}

	/** @test */
	public function search_reactions()
	{
		$user = User::find(2);
		$data = [
			'query' => [
				'where' => [
					['reactable_type', 'App\\Models\\Slide'],
				],
				'whereIn' => ['reactable_id', [1, 2, 3, 6, 10]]
			],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/reactables/.search'), $data);

		$response
			->assertStatus(200);
	}
}
