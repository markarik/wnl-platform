<?php


namespace Tests\Api\Coupons;


use App\Events\Coupons\CouponCreated;
use App\Events\Coupons\CouponDeleted;
use App\Events\Coupons\CouponUpdated;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\Api\ApiTestCase;

class CouponsTest extends ApiTestCase
{
	use DatabaseTransactions;

	protected function url($path)
	{
		return '/api/v1' . $path;
	}

	/** @test */
	public function create_coupon_no_token_fails()
	{
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		$this
			->json('POST', $this->url('/coupons'), [])
			->assertStatus(403);
	}

	/** @test */
	public function create_coupon_success()
	{
		Event::fake();

		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		$response = $this
			->withHeader(config('coupons.coupons_sync_header'), config('coupons.coupons_sync_token'))
			->json('POST', $this->url('/coupons'), [
				'coupon' => [
					'code' => 'foo',
					'value' => 10,
					'type' => 'amount'
				]
			]);

		$response->assertStatus(200);
		$response->assertJson([
			'code' => 'foo',
			'value' => 10,
			'type' => 'amount'
		]);

		Event::assertNotDispatched(CouponCreated::class);
	}

	/** @test */
	public function update_coupon_no_token_fails()
	{
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		$this
			->json('PUT', $this->url('/coupons'), [])
			->assertStatus(403);
	}

	/** @test */
	public function update_not_existing_coupon_fails()
	{
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		$this
			->withHeader(config('coupons.coupons_sync_header'), config('coupons.coupons_sync_token'))
			->json('PUT', $this->url('/coupons'), [
				'coupon' => [
					'code' => 'foo',
					'value' => 10,
					'type' => 'amount'
				]
			])
			->assertStatus(404);
	}

	/** @test */
	public function update_coupon_success()
	{
		Event::fake();

		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		Coupon::create([
			'code' => 'foo',
			'value' => 10,
			'type' => 'amount',
			'times_usable' => 1
		]);

		$response = $this
			->withHeader(config('coupons.coupons_sync_header'), config('coupons.coupons_sync_token'))
			->json('PUT', $this->url('/coupons'), [
				'coupon' => [
					'code' => 'foo',
					'times_usable' => 0,
					'type' => 'amount',
					'value' => 10
				]
			]);

		$response->assertStatus(200);
		$response->assertJson([
			'code' => 'foo',
			'times_usable' => 0,
			'type' => 'amount',
			'value' => 10
		]);

		Event::assertNotDispatched(CouponUpdated::class);
	}

	/** @test */
	public function delete_coupon_no_token_fails()
	{
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		$this
			->json('DELETE', $this->url('/coupons'), [])
			->assertStatus(403);
	}

	/** @test */
	public function delete_not_existing_coupon_fails()
	{
		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		$this
			->withHeader(config('coupons.coupons_sync_header'), config('coupons.coupons_sync_token'))
			->json('DELETE', $this->url('/coupons'), [
				'coupon' => [
					'code' => 'foo',
					'value' => 10,
					'type' => 'amount'
				]
			])
			->assertStatus(404);
	}

	/** @test */
	public function delete_coupon_success()
	{
		Event::fake();

		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		$createdCoupon = Coupon::create([
			'code' => 'foo',
			'value' => 10,
			'type' => 'amount',
			'times_usable' => 1
		]);

		$response = $this
			->withHeader(config('coupons.coupons_sync_header'), config('coupons.coupons_sync_token'))
			->json('DELETE', $this->url('/coupons'), [
				'coupon' => $createdCoupon
			]);

		$response->assertStatus(200);

		Event::assertNotDispatched(CouponDeleted::class);
	}

	/** @test */
	public function delete_coupon_with_duplicated_code_returns_error()
	{
		Event::fake();

		$expectedToken = '123';
		Config::set('coupons.coupons_sync_token', $expectedToken);

		Coupon::create([
			'code' => 'foo',
			'value' => 10,
			'type' => 'amount',
			'times_usable' => 1
		]);

		$createdCoupon = Coupon::create([
			'code' => 'foo',
			'value' => 10,
			'type' => 'amount',
			'times_usable' => 1
		]);

		$response = $this
			->withHeader(config('coupons.coupons_sync_header'), config('coupons.coupons_sync_token'))
			->json('DELETE', $this->url('/coupons'), [
				'coupon' => $createdCoupon
			]);

		$response->assertStatus(400);
	}
}
