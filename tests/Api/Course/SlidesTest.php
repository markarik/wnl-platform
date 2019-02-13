<?php

namespace Tests\Api\Course;

use App\Models\Role;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;


class SlidesTest extends ApiTestCase
{
	use DatabaseTransactions;

	/** @test */
	public function post_slide()
	{
		$this->markTestSkipped();
		$user = factory(User::class)->create();
		$adminRole = Role::byName('admin');
		$user->roles()->attach($adminRole);
		$screen = factory(Screen::class)->create();

		$data = [
			'content'       => '<section>siema</section>',
			'is_functional' => false,
			'order_number'  => 20,
			'screen'        => $screen->id,
		];

		$response = $this
			->actingAs($user)
			->json('POST', $this->url('/slides'), $data);
		$response->dump();
		$response
			->assertStatus(200);
	}

}
