<?php

namespace Tests\Api\Slides;

use App\Models\Reactable;
use App\Models\Reaction;
use App\Models\Slide;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class ReactablesTest extends ApiTestCase
{

	use DatabaseTransactions;

	public function test_get_users_saved_slides()
	{
		$user = factory(User::class)->create();

		UserSubscription::create([
			'user_id' => $user->id,
			'access_start' => Carbon::yesterday(),
			'access_end' => Carbon::tomorrow()
		]);

		$bookmark = Reaction::type('bookmark');
		$bookmarkedSlides = factory(Slide::class, 10)->create();
		$watch = Reaction::type('watch');
		$watchedSlides = factory(Slide::class, 10)->create();
		$otherSlides = factory(Slide::class, 10)->create();
		$allSlides = $bookmarkedSlides->concat($watchedSlides)->concat($otherSlides);

		$bookmarkedSlides->each(function($slide) use ($bookmark, $user) {
			$slide->reactions()->attach($bookmark, [
				'user_id' => $user->id,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			]);
		});

		$watchedSlides->each(function($slide) use ($watch, $user) {
			$slide->reactions()->attach($watch, [
				'user_id' => $user->id,
				'created_at' => Carbon::now(),
				'updated_at' => Carbon::now()
			]);
		});

		$response = $this
			->actingAs($user)
			->json(
				'POST',
				$this->url("/reactables/current/savedSlides"),
				[
					'slideIds' => $allSlides->pluck('id')
				]
			);

		$bookmarks = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->whereIn('reactable_id', $bookmarkedSlides->pluck('id'))
			->get();

		$watchs = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->whereIn('reactable_id', $watchedSlides->pluck('id'))
			->get();

		$others = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->whereIn('reactable_id', $otherSlides->pluck('id'))
			->get();

		$expectedResults = $bookmarks->concat($watchs)->map(function($reactable) {
			return [
				'id'             => $reactable->id,
				'user_id'        => $reactable->user_id,
				'reaction_id'    => $reactable->reaction_id,
				'reactable_id'   => $reactable->reactable_id,
				'reactable_type' => $reactable->reactable_type,
				'created_at'     => $reactable->created_at->timestamp,
				'updated_at'     => $reactable->updated_at->timestamp,
			];
		});
		$unexpectedResults = $others->map(function($reactable) {
			return [
				'id'             => $reactable->id,
				'user_id'        => $reactable->user_id,
				'reaction_id'    => $reactable->reaction_id,
				'reactable_id'   => $reactable->reactable_id,
				'reactable_type' => $reactable->reactable_type,
				'created_at'     => $reactable->created_at->timestamp,
				'updated_at'     => $reactable->updated_at->timestamp,
			];
		});

		$response->assertJson($expectedResults->toArray());
		$response->assertJsonMissing($unexpectedResults->toArray());
	}
}
