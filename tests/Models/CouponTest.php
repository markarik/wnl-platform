<?php

namespace Tests\Models;

use App\Events\Coupons\CouponCreated;
use App\Events\Coupons\CouponDeleted;
use App\Events\Coupons\CouponUpdated;
use App\Models\Coupon;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;


class CouponTest extends TestCase
{

	use DatabaseTransactions;

	/** @test */
	public function creating_coupon_dispatches_event()
	{
		Event::fake();

		Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);

		Event::assertDispatchedTimes(CouponCreated::class, 1);
		Event::assertNotDispatched(CouponUpdated::class);
		Event::assertNotDispatched(CouponDeleted::class);
	}

	/** @test */
	public function updating_coupon_dispatches_event()
	{
		Event::fake();

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->value = 100;
		$coupon->save();

		Event::assertDispatchedTimes(CouponCreated::class, 1);
		Event::assertDispatchedTimes(CouponUpdated::class, 1);
		Event::assertNotDispatched(CouponDeleted::class);
	}

	/** @test */
	public function deleting_coupon_dispatches_event()
	{
		Event::fake();

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->delete();

		Event::assertDispatchedTimes(CouponCreated::class, 1);
		Event::assertDispatchedTimes(CouponDeleted::class, 1);
		Event::assertNotDispatched(CouponUpdated::class);
	}

	/** @test */
	public function creating_coupon_performs_sync() {
		$spyClient = Mockery::spy(Client::class);

		Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);

		$spyClient->shouldHaveReceived('request')->once();
	}
}
