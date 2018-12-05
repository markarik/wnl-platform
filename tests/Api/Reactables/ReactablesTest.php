<?php

namespace Tests\Api\Slides;

use App\Http\Controllers\Api\Transformers\ReactableTransformer;
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

	/** @test */
	public function get_users_saved_slides()
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

		$transformer = new ReactableTransformer();

		$bookmarkReactables = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->whereIn('reactable_id', $bookmarkedSlides->pluck('id'))
			->get();
		$bookmarks = $bookmarkReactables->map(function($reactable) use ($transformer) {
			return $transformer->transform($reactable);
		});

		$watchReactables = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->whereIn('reactable_id', $watchedSlides->pluck('id'))
			->get();
		$watchs = $watchReactables->map(function($reactable) use ($transformer) {
			return $transformer->transform($reactable);
		});

		$otherReactables = Reactable::where('reactable_type', 'App\\Models\\Slide')
			->whereIn('reactable_id', $otherSlides->pluck('id'))
			->get();
		$others = $otherReactables->map(function($reactable) use ($transformer) {
			return $transformer->transform($reactable);
		});

		$response->assertJson($bookmarks->concat($watchs)->toArray());
		$response->assertJsonMissing($others->toArray());
	}
}
