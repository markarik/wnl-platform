<?php

namespace Tests\Unit\Commands;

use App\Mail\AccountSuspendedUnpaidInstalment;
use App\Mail\InstalmentReminder;
use App\Mail\TransferReminder;
use App\Models\Order;
use App\Models\OrderInstalment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrdersHandleUnpaidTestTest extends TestCase
{
	use DatabaseTransactions;

	public function testNoRemindersSent()
	{
		$order = factory(Order::class)->create([
			'paid' => false,
			'created_at' => Carbon::now()->subDays(3),
			'method' => 'online',
			'canceled' => false
		]);

		Mail::fake();
		Artisan::call('orders:handleUnpaid');

		$this->assertDatabaseMissing('payment_reminders', ['order_id' => $order->id]);
		Mail::assertNothingQueued();
	}

	public function testUnpaidOrder()
	{
		/** @var Order $order */
		$order = factory(Order::class)->create([
			'paid' => false,
			'created_at' => Carbon::now()->subDays(7),
			'method' => 'online',
			'canceled' => false
		]);

		// First run
		Mail::fake();
		Artisan::call('orders:handleUnpaid');

		$this->assertDatabaseHas('payment_reminders', ['order_id' => $order->id]);

		Mail::assertQueued(TransferReminder::class, function ($mail) use ($order) {
			return $mail->order->id === $order->id;
		});
		Mail::assertQueued(TransferReminder::class, 1);

		// Second run
		Mail::fake();
		Artisan::call('orders:handleUnpaid');
		Mail::assertNothingQueued();

		// Third run
		Mail::fake();
		Artisan::call('orders:handleUnpaid', [
			// 4 would be sufficient if we didn't filter out weekends
			'--time-shift' => 6
		]);

		$order = $order->fresh();
		$this->assertTrue($order->canceled);
		Mail::assertNothingQueued();
	}

	public function testTwoOrdersForOneProduct()
	{
		/** @var Product $product */
		$product = factory(Product::class)->create();

		/** @var User $user */
		$user = factory(Product::class)->create();

		/** @var Order $orderPaid */
		$orderPaid = factory(Order::class)->create([
			'product_id' => $product->id,
			'user_id' => $user->id,
			'paid' => true,
			'created_at' => Carbon::now()->subDays(7),
			'method' => 'online',
			'canceled' => false,
		]);

		/** @var Order $orderUnpaid */
		$orderUnpaid = factory(Order::class)->create([
			'product_id' => $product->id,
			'user_id' => $user->id,
			'paid' => false,
			'created_at' => Carbon::now()->subDays(7),
			'method' => 'online',
			'canceled' => false,
		]);

		// First run
		Mail::fake();
		Artisan::call('orders:handleUnpaid');

		Mail::assertNothingQueued();

		$orderPaid = $orderPaid->fresh();
		$this->assertFalse($orderPaid->canceled);

		$orderUnpaid = $orderUnpaid->fresh();
		$this->assertTrue($orderUnpaid->canceled);

		// Second run
		Mail::fake();
		Artisan::call('orders:handleUnpaid', [
			'--time-shift' => 10
		]);
		Mail::assertNothingQueued();
	}



	public function testUnpaidInstalment()
	{
		/** @var User $user */
		$user = factory(User::class)->create([
			'suspended' => false,
		]);

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'user_id' => $user->id,
			'paid' => true,
			'created_at' => Carbon::now()->subDays(60),
			'method' => 'instalments',
			'canceled' => false
		]);

		factory(OrderInstalment::class)->create([
			'order_id' => $order->id,
			'due_date' => Carbon::now()->subDays(53),
			'paid_amount' => 750.0,
			'amount' => 750.0,
			'order_number' => 1,
		]);

		$orderInstalmentUnpaid = factory(OrderInstalment::class)->create([
			'order_id' => $order->id,
			'due_date' => Carbon::now()->addDays(1),
			'paid_amount' => 0.0,
			'amount' => 350.0,
			'order_number' => 2,
		]);

		// First run
		Mail::fake();
		Artisan::call('orders:handleUnpaid');

		$this->assertDatabaseMissing('payment_reminders', ['order_id' => $order->id]);
		Mail::assertNothingQueued();

		// Second run
		Mail::fake();
		Artisan::call('orders:handleUnpaid', [
			'--time-shift' => 1,
		]);

		$this->assertDatabaseHas('payment_reminders', ['order_id' => $order->id, 'instalment_number' => 2]);
		Mail::assertQueued(InstalmentReminder::class, function ($mail) use ($orderInstalmentUnpaid) {
			return $mail->instalment->id === $orderInstalmentUnpaid->id;
		});
		Mail::assertQueued(InstalmentReminder::class, 1);

		// Third run
		Mail::fake();
		Artisan::call('orders:handleUnpaid', [
			'--time-shift' => 6,
		]);

		$user = $user->fresh();
		$this->assertTrue($user->suspended);
		Mail::assertQueued(AccountSuspendedUnpaidInstalment::class, function ($mail) use ($orderInstalmentUnpaid) {
			return $mail->instalment->id === $orderInstalmentUnpaid->id;
		});

		// Fourth run
		Mail::fake();
		Artisan::call('orders:handleUnpaid', [
			'--time-shift' => 20,
		]);

		Mail::assertNothingQueued();
	}
}
