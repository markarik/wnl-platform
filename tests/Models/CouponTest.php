<?php

namespace Tests\Models;

use App\Events\Coupons\CouponCreated;
use App\Events\Coupons\CouponDeleted;
use App\Events\Coupons\CouponUpdated;
use App\Jobs\SyncCouponUpdate;
use App\Models\Coupon;
use Facades\App\Contracts\Requests;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
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
	public function updating_coupon_dispatches_job()
	{
		Event::fake([
			CouponCreated::class,
			CouponDeleted::class
		]);
		Bus::fake();

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->value = 100;
		$coupon->save();

		Event::assertDispatchedTimes(CouponCreated::class, 1);
		Event::assertNotDispatched(CouponDeleted::class);

		Bus::assertDispatched(SyncCouponUpdate::class);
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
		$expectedUrl = 'http://platform.test';
		$expectedToken = '123';

		$coupon = new Coupon();
		$coupon->code = 'foo';
		$coupon->value = 10;
		$coupon->type = 'amount';
		$coupon->times_usable = 10;

		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_is_source', true);

		$couponToSync = $coupon->toArray();
		unset($couponToSync['id']);

		$mocked = Requests::shouldReceive('request')->once();

		$coupon->save();
		$mocked->verify();
	}

	/** @test */
	public function deleting_coupon_performs_sync() {
		$expectedUrl = 'http://platform.test';
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_is_source', true);

		$mocked = Requests::shouldReceive('request');

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$couponToSync = $coupon->toArray();

		$mocked->withArgs([
			'DELETE',
			$expectedUrl . "/api/v1/coupons",
			[
				'Accept' => 'application/json',
				config('coupons.coupons_sync_header') => $expectedToken,
			],
			$body = [
				'coupon' => $couponToSync
			],
		]);

		$coupon->delete();

		$mocked->verify();
	}

	/** @test */
	public function updating_coupon_performs_sync() {
		$expectedUrl = 'http://platform.test';
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_is_source', true);

		$mocked = Requests::shouldReceive('request');

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->times_usable = 10;
		$couponToSync = $coupon->toArray();
		unset($couponToSync['id']);

		$mocked->withArgs([
				'PUT',
				$expectedUrl . "/api/v1/coupons",
				[
					'Accept' => 'application/json',
					config('coupons.coupons_sync_header') => $expectedToken,
				],
				$body = [
					'coupon' => $couponToSync
				]
		]);

		$coupon->save();
		$mocked->verify();
	}

	/** @test */
	public function creating_coupon_does_not_perform_sync_for_no_source() {
		$mocked = Requests::shouldReceive('request');
		$expectedUrl = 'http://platform.test';
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_is_source', false);

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$couponToSync = $coupon->toArray();
		unset($couponToSync['id']);

		$mocked->never()->verify();
	}

	/** @test */
	public function updating_coupon_performs_sync_for_no_source() {
		$expectedUrl = 'http://platform.test';
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_is_source', false);

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->times_usable = 10;

		$mocked = Requests::shouldReceive('request');

		$coupon->save();
		$mocked->verify();
	}

	/** @test */
	public function updating_coupon_without_url_does_not_throw() {
		Config::set('coupons.coupons_sync_url', null);

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->times_usable = 10;

		$mocked = Requests::shouldReceive('request')->never();

		$coupon->save();
		$mocked->verify();
	}

	/** @test */
	public function deleting_coupon_does_not_perform_sync_for_no_source() {
		$mocked = Requests::shouldReceive('request');
		$expectedUrl = 'http://platform.test';
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_url', $expectedUrl);
		Config::set('coupons.coupons_sync_token', $expectedToken);
		Config::set('coupons.coupons_sync_is_source', false);

		$coupon = Coupon::create(['code' => 'fizzbuzz', 'type' => 'amount', 'value' => 10]);
		$coupon->delete();

		$mocked->never()->verify();
	}
}
