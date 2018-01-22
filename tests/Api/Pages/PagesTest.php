<?php

namespace Tests\Api\Qna;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class PagesTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function get_page()
	{
		$user = factory(User::class)->create();
		$page = factory(Page::class)->create();

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/pages/' . $page->slug));

		$response
			->assertStatus(200)
			->assertJson([
				'id'      => $page->id,
				'content' => $page->content,
				'name'    => $page->name,
			]);
	}
}
