<?php

namespace Tests\Unit\Jobs;

use App\Jobs\OrderPaid;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class OrderPaidTest extends TestCase
{
	public function testOrderPaidOnlineAndThen100PercentCouponAdded() {
		$this->fakeIt();

		/** @var Order $order */
		$order = factory(Order::class)->create();

		factory(Payment::class)->create([
			'order_id' => $order->id,
			'status' => Payment::STATUS_SUCCESS,
		]);

		$job = new OrderPaid($order);
		$job->handle();

		$coupon = factory(Coupon::class)->create([
			'kind' => Coupon::KIND_VOUCHER,
			'type' => 'percentage',
			'value' => 100,
		]);
		$order->attachCoupon($coupon);

		$job = new OrderPaid($order->fresh());
		$job->handle();

		$this->assertEquals(1, $order->fresh()->invoices->where('type', '=', 'vat')->count());
	}

	public function testTwoInstalmentsPaidAndThen100PercentCouponAdded() {
		$this->fakeIt();

		/** @var Product $product */
		$product = factory(Product::class)->create([
			// Skip instalments note to avoid a lot of mocking
			'access_end' => null,
		]);

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'product_id' => $product->id,
			'method' => Order::PAYMENT_METHOD_INSTALMENTS,
		]);

		factory(Payment::class)->create([
			'order_id' => $order->id,
			'status' => Payment::STATUS_SUCCESS,
		]);

		$job = new OrderPaid($order);
		$job->handle();

		factory(Payment::class)->create([
			'order_id' => $order->id,
			'status' => Payment::STATUS_SUCCESS,
		]);

		$job = new OrderPaid($order);
		$job->handle();

		$coupon = factory(Coupon::class)->create([
			'kind' => Coupon::KIND_VOUCHER,
			'type' => 'percentage',
			'value' => 100,
		]);
		$order->attachCoupon($coupon);

		$job = new OrderPaid($order->fresh());
		$job->handle();

		$this->assertEquals(2, $order->fresh()->invoices->where('type', '=', 'vat')->count());
	}

	public function testThreeInstalmentsPaid() {
		$this->fakeIt();

		/** @var Product $product */
		$product = factory(Product::class)->create([
			// Skip instalments note to avoid a lot of mocking
			'access_end' => null,
		]);

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'product_id' => $product->id,
			'method' => Order::PAYMENT_METHOD_INSTALMENTS,
		]);

		factory(Payment::class)->create([
			'order_id' => $order->id,
			'status' => Payment::STATUS_SUCCESS,
		]);

		$job = new OrderPaid($order);
		$job->handle();

		factory(Payment::class)->create([
			'order_id' => $order->id,
			'status' => Payment::STATUS_SUCCESS,
		]);

		$job = new OrderPaid($order);
		$job->handle();

		factory(Payment::class)->create([
			'order_id' => $order->id,
			'status' => Payment::STATUS_SUCCESS,
		]);

		$job = new OrderPaid($order->fresh());
		$job->handle();

		$this->assertEquals(3, $order->fresh()->invoices->where('type', '=', 'vat')->count());
	}

	private function fakeIt(): void
	{
		// We don't want OrderObserver to interfere
		Order::unsetEventDispatcher();
		Storage::fake();
		Mail::fake();

		// Warning: overload impacts global state but I don't expect it to be a problem
		// See http://docs.mockery.io/en/stable/cookbook/mocking_hard_dependencies.html
		Mockery::mock('overload:Dompdf\Dompdf')->shouldReceive('setBasePath');
		$pdfMock = Mockery::mock('overload:Barryvdh\DomPDF\PDF');
		$pdfMock->shouldReceive('loadHtml');
		$pdfMock->shouldReceive('setPaper');
		$pdfMock->shouldReceive('output');
	}
}
