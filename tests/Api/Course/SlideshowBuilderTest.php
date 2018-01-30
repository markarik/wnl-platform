<?php

namespace Tests\Api\Course;

use App\Models\Screen;
use App\Models\Section;
use App\Models\Slide;
use App\Models\Slideshow;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class SlideshowBuilderTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function slideshow_builder_query()
	{
		$this->markTestSkipped();
		$user = factory(User::class)->create();
		$slides = factory(Slide::class, 20)->create();
		$slideshow = factory(Slideshow::class)->create();
		$screen = factory(Screen::class)->create([
			'meta' => [
				'resources' => [
					[
						"id"   => $slideshow->id,
						"name" => "slideshows",
					]
				],
			],
		]);

		$section = factory(Section::class)->create([
			'screen_id' => $screen->id,
		]);
		$slideshow->slides()->attach($slides);
		$section->slides()->attach($slides);

		$data = [
			'query' => [
				'whereIn' => ['id', $slides->pluck('id')->toArray()],
			],
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/slideshow_builder/.query'), $data);

		$response
			->assertStatus(200);
	}

}
