<?php

namespace Tests\Api\Quiz;

use App\Models\Tag;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class TagsTest extends ApiTestCase
{

	use DatabaseTransactions;

	/** @test * */
	public function search_tags()
	{
		$user = factory(User::class)->create();

		UserSubscription::create([
			'user_id' => $user->id,
			'access_start' => Carbon::yesterday(),
			'access_end' => Carbon::tomorrow()
		]);

		$tags = factory(Tag::class, 10)->create();
		$matchingTag = factory(Tag::class)->create([
			'name' => 'fizz'
		]);
		$excludedTags = $tags->slice(0, 5);

		$data = [
			'name' => 'iz',
			'excludedIds' => $excludedTags->pluck('id')
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/tags/byName'), $data);

		$response->assertStatus(200);

		$this->assertContains([
			'id' => $matchingTag->id,
			'name' => $matchingTag->name,
			'description' => null,
			'color' => null
		], $response->json());

		$response->assertJsonMissing($excludedTags->map(function ($tag) {
			return [
				'name' => $tag->name,
				'id' => $tag->id
			];
		})->toArray());
	}
}
