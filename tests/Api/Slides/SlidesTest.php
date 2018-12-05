<?php

namespace Tests\Api\Slides;

use App\Http\Controllers\Api\Transformers\SlideTransformer;
use App\Models\Category;
use App\Models\Slide;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class SlidesTest extends ApiTestCase
{

	use DatabaseTransactions;

	/** @test */
	public function get_slides_by_tag_name()
	{
		$user = factory(User::class)->create();

		UserSubscription::create([
			'user_id' => $user->id,
			'access_start' => Carbon::yesterday(),
			'access_end' => Carbon::tomorrow()
		]);

		$tags = factory(Tag::class, 2)->create();
		$categories = factory(Category::class, 2)->create();
		$allSlides = collect();

		$categories->each(function ($category, $key) use ($tags, &$allSlides) {
			$slides = factory(Slide::class, 10)->create()->each(function($slide) use ($tags, $key, &$allSlides) {
				$slide->tags()->attach($tags->get($key));
			});
			$category->slides()->attach($slides);
			$category->name = $tags->get($key);
			$allSlides = $allSlides->concat($slides);

			$category->save();
		});

		$desiredTag = $tags->first();
		$response = $this
			->actingAs($user)
			->json(
				'POST',
				$this->url("/slides/category/{$desiredTag->name}"),
				[
					'slideIds' => $allSlides->pluck('id')
				]
			);

		$transformer = new SlideTransformer();

		$expectedResponse = $categories->first()->slides->map(function($slide) use ($transformer) {
			return $transformer->transform($slide);
		});

		$response->assertJson($expectedResponse->toArray());
	}
}
