<?php

namespace Tests\Models;

use App\Events\Coupons\CouponCreated;
use App\Events\Coupons\CouponDeleted;
use App\Events\Coupons\CouponUpdated;
use App\Models\Coupon;
use Facades\App\Contracts\Requests;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
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
		$mocked = Requests::shouldReceive('request');

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$couponToSync = $coupon->toArray();
		unset($couponToSync['id']);

		$expectedUrl = 'http://platform.test';
		$expectedHost = 'platform.test';
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_host', $expectedHost);
		Config::set('coupons.coupons_sync_source', true);

		$mocked->withArgs([
			'POST',
			$expectedUrl . "/api/v1/coupons",
			[
				'Accept' => 'application/json',
				'Host' => $expectedToken,
				Coupon::SYNC_TOKEN_HEADER => $expectedToken,
			],
			$body = [
				'coupon' => $couponToSync
			]
		])->verify();
	}

	/** @test */
	public function deleting_coupon_performs_sync() {
		$mocked = Requests::shouldReceive('request');

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->delete();

		$expectedUrl = 'http://platform.test';
		$expectedHost = 'platform.test';
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_host', $expectedHost);
		Config::set('coupons.coupons_sync_source', true);

		$mocked->withArgs([
			'DELETE',
			$expectedUrl . "/api/v1/coupons",
			[
				'Accept' => 'application/json',
				'Host' => $expectedToken,
				Coupon::SYNC_TOKEN_HEADER => $expectedToken,
			],
			$body = [
				'coupon' => $coupon
			]
		])->verify();
	}

	/** @test */
	public function updating_coupon_performs_sync() {
		$mocked = Requests::shouldReceive('request');

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->times_usable = 10;
		$coupon->save();
		$couponToSync = $coupon->toArray();
		unset($couponToSync['id']);

		$expectedUrl = 'http://platform.test';
		$expectedHost = 'platform.test';
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_host', $expectedHost);
		Config::set('coupons.coupons_sync_source', true);

		$mocked->withArgs([
			'PUT',
			$expectedUrl . "/api/v1/coupons",
			[
				'Accept' => 'application/json',
				'Host' => $expectedToken,
				Coupon::SYNC_TOKEN_HEADER => $expectedToken,
			],
			$body = [
				'coupon' => $couponToSync
			]
		])->verify();
	}
}
