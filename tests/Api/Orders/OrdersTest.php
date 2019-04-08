<?php

namespace Tests\Api\Products;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;

class OrdersTest extends ApiTestCase
{
	use DatabaseTransactions;

	public function testUserCanGetOrder()
	{
		$user = factory(User::class)->create();
		$order = factory(Order::class)->create([
			'user_id' => $user->id
		]);

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/orders/' . $order->id));

		$response->assertStatus(200);
	}

	public function testAdminCanGetOtherUserOrder()
	{
		$order = factory(Order::class)->create();
		$admin = factory(User::class)->create();
		$admin->roles()->attach(Role::byName('admin'));

		$response = $this
			->actingAs($admin)
			->json('GET', $this->url('/orders/' . $order->id));

		$response->assertStatus(200);
	}

	public function testUserCantGetOtherUserOrder()
	{
		$order = factory(Order::class)->create();
		$user = factory(User::class)->create();

		$response = $this
			->actingAs($user)
			->json('GET', $this->url('/orders/' . $order->id));

		$response->assertStatus(403);
	}

}
