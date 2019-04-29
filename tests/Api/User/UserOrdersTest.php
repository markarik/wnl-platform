<?php

namespace Tests\Api\User;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\ApiTestCase;

class UserOrdersTest extends ApiTestCase
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
			->json('GET', $this->url('/users/' . $user->id . '/orders/' . $order->id));

		$response->assertStatus(200);
	}

	public function testAdminCanGetOtherUserOrder()
	{
		$user = factory(User::class)->create();
		$order = factory(Order::class)->create([
			'user_id' => $user->id
		]);
		$admin = factory(User::class)->create();
		$admin->roles()->attach(Role::byName('admin'));

		$response = $this
			->actingAs($admin)
			->json('GET', $this->url('/users/' . $user->id . '/orders/' . $order->id));

		$response->assertStatus(200);
	}

	public function testUserCantGetOtherUserOrder()
	{
		$user = factory(User::class)->create();
		$order = factory(Order::class)->create([
			'user_id' => $user->id
		]);
		$anotherUser = factory(User::class)->create();


		$response = $this
			->actingAs($anotherUser)
			->json('GET', $this->url('/users/' . $user->id . '/orders/' . $order->id));

		$response->assertStatus(403);
	}

	public function testUserCanAttachCouponToOrder()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create([
			'price' => 1500
		]);
		$order = factory(Order::class)->create([
			'user_id' => $user->id,
			'product_id' => $product->id,
		]);
		$coupon = factory(Coupon::class)->create([
			'type' => 'percentage',
			'value' => 10
		]);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url('/users/' . $user->id . '/orders/' . $order->id . '/coupon'), [
				'code' => $coupon->code
			]);

		$response->assertStatus(200);
		$this->assertEquals(1350.00, $order->fresh()->total_with_coupon);
	}

	public function testUserCantAttachLowerCouponToOrder()
	{
		$user = factory(User::class)->create();
		$product = factory(Product::class)->create([
			'price' => 1500
		]);
		$order = factory(Order::class)->create([
			'user_id' => $user->id,
			'product_id' => $product->id,
		]);
		$coupon = factory(Coupon::class)->create([
			'type' => 'percentage',
			'value' => 10
		]);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url('/users/' . $user->id . '/orders/' . $order->id . '/coupon'), [
				'code' => $coupon->code
			]);

		$response->assertStatus(200);
		$this->assertEquals(1350.00, $order->fresh()->total_with_coupon);


		$lowerCoupon = factory(Coupon::class)->create([
			'type' => 'percentage',
			'value' => 5
		]);

		$response = $this
			->actingAs($user)
			->json('PUT', $this->url('/users/' . $user->id . '/orders/' . $order->id . '/coupon'), [
				'code' => $lowerCoupon->code
			]);

		$response->assertStatus(422);
		$this->assertEquals(1350.00, $order->fresh()->total_with_coupon);
	}
}
