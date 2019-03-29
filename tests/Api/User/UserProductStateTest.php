<?php

namespace Tests\Api\User;

use App\Models\Lesson;
use App\Models\LessonProduct;
use App\Models\Order;
use App\Models\User;
use Tests\Api\ApiTestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserProductStateTest extends ApiTestCase
{
	use DatabaseTransactions;

	public function testUpdateForLatestProductCurrent()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		/** @var Lesson $lesson */
		$lesson = factory(Lesson::class)->create();

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 1,
		]);

		factory(LessonProduct::class)->create([
			'product_id' => $order->product_id,
			'lesson_id' => $lesson->id,
			'start_date' => Carbon::now()->subDays(100)
		]);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/users/current/user_product_state/latest"), [
				'onboarding_step' => 'finished',
			]);

		$response->assertStatus(200);

		$json = $response->json();
		$this->assertEquals($order->product_id, $json['product_id']);
		$this->assertEquals('finished', $json['onboarding_step']);
	}

	public function testUpdateForLatestProductSelf()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		/** @var Lesson $lesson */
		$lesson = factory(Lesson::class)->create();

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => 1,
		]);

		factory(LessonProduct::class)->create([
			'product_id' => $order->product_id,
			'lesson_id' => $lesson->id,
			'start_date' => Carbon::now()->subDays(100)
		]);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/users/$user->id/user_product_state/latest"), [
				'onboarding_step' => 'finished',
			]);

		$response->assertStatus(200);

		$json = $response->json();
		$this->assertEquals($order->product_id, $json['product_id']);
		$this->assertEquals('finished', $json['onboarding_step']);
	}

	public function testUpdateForLatestProductOtherUser()
	{
		/** @var User $user */
		$user = factory(User::class)->create();

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url("/users/1/user_product_state/latest"), [
				'onboarding_step' => 'finished',
			]);

		$response->assertStatus(403);
	}
}
