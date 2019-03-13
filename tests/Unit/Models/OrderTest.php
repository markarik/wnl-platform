<?php

namespace Tests\Unit\Http\Controllers\Api\Serializer;

use App\Models\Order;
use App\Models\OrderInstalment;
use App\Models\Product;
use App\Models\ProductInstalment;
use Carbon\Carbon;
use Tests\TestCase;

class OrderTest extends TestCase
{
	public function testGetInstalmentsAttributeAllPaid() {
		$order = $this->createOrder(1000, 1000);

		$this->assertEquals([
			'allPaid' => true,
			'instalments' => []
		], $order->instalments);
	}

	/**
	 * @dataProvider getInstalmentsAttributeDataProvider
	 * @param float $price
	 * @param float $paidAmount
	 * @param array $orderInstalments
	 * @param float $expectedTotalLeft
	 * @param array $expectedInstalments
	 * @param array $expectedNextPayment
	 */
	public function testGetInstalmentsAttributeNotPaid($price, $paidAmount, $orderInstalments, $expectedTotalLeft, $expectedInstalments, $expectedNextPayment) {
		$order = $this->createOrder($price, $paidAmount);

		foreach ($orderInstalments as $orderInstalment) {
			factory(OrderInstalment::create(array_merge($orderInstalment, ['order_id' => $order->id])));
		}

		$instalments = $order->instalments['instalments'];
		$this->assertCount(3, $instalments);

		for ($i = 0; $i < count($instalments); $i++) {
			$this->assertInstalmentData(
				$expectedInstalments[$i]['due_date'],
				$expectedInstalments[$i]['amount'],
				$expectedInstalments[$i]['left'],
				$instalments[$i]
			);
		}

		/** @var OrderInstalment $nextPayment */
		$nextPayment = $order->instalments['nextPayment'];

		$this->assertTrue($expectedNextPayment['due_date']->isSameDay($nextPayment->due_date));
		$this->assertEquals($expectedNextPayment['left'], $nextPayment->left_amount);

		$this->assertEquals(false, $order->instalments['allPaid']);
		$this->assertEquals($expectedTotalLeft, $order->instalments['total']);
	}

	private function createOrder(float $price, float $paidAmount): Order
	{
		/** @var Product $product */
		$product = factory(Product::class)->create([
			'price' => $price
		]);

		/** @var Order $order */
		$order = factory(Order::class)->create([
			'created_at' => Carbon::now(),
			'paid_amount' => $paidAmount,
			'product_id' => $product->id,
		]);

		factory(ProductInstalment::class)->create([
			'value' => 50.0,
			'due_days' => 7,
			'order_number' => 1,
			'product_id' => $product
		]);

		factory(ProductInstalment::class)->create([
			'value' => 50.0,
			'due_date' => $this->getDueDateForSecondInstalment(),
			'order_number' => 2,
			'product_id' => $product
		]);

		factory(ProductInstalment::class)->create([
			'value' => 100.0,
			'due_date' => $this->getDueDateForThirdInstalment(),
			'order_number' => 3,
			'product_id' => $product
		]);

		return $order;
	}

	private function assertInstalmentData(Carbon $expectedDate, float $expectedAmount, float $expectedLeft, OrderInstalment $instalment): void
	{
		$this->assertTrue($expectedDate->isSameDay($instalment->due_date));
		$this->assertEquals($expectedAmount, $instalment->amount);
		$this->assertEquals($expectedLeft, $instalment->left_amount);
	}

	public function getInstalmentsAttributeDataProvider()
	{
		$dueDateForFirstInstalment = Carbon::now()->addDays(7);
		$dueDateForSecondInstalment = $this->getDueDateForSecondInstalment();
		$dueDateForThirdInstalment = $this->getDueDateForThirdInstalment();

		return [
			[
				1000.0,
				0.0,
				[],
				1000.0,
				[
					[
						'due_date' => $dueDateForFirstInstalment,
						'amount' => 500.0,
						'left' => 500.0,
					],
					[
						'due_date' => $dueDateForSecondInstalment,
						'amount' => 250.0,
						'left' => 250.0,
					],
					[
						'due_date' => $dueDateForThirdInstalment,
						'amount' => 250.0,
						'left' => 250.0,
					],
				],
				[
					'due_date' => $dueDateForFirstInstalment,
					'amount' => 500.0,
					'left' => 500.0,
				]
			],
			[
				1000.0,
				500.0,
				[
					[
						'due_date' => $dueDateForFirstInstalment,
						'amount' => 500.0,
						'paid_amount' => 500.0,
						'order_number' => 1,
					],
					[
						'due_date' => $dueDateForSecondInstalment,
						'amount' => 250.0,
						'paid_amount' => 0.0,
						'order_number' => 2,
					],
					[
						'due_date' => $dueDateForThirdInstalment,
						'amount' => 250.0,
						'paid_amount' => 0.0,
						'order_number' => 3,
					],
				],
				500.0,
				[
					[
						'due_date' => $dueDateForFirstInstalment,
						'amount' => 500.0,
						'left' => 0.0,
					],
					[
						'due_date' => $dueDateForSecondInstalment,
						'amount' => 250.0,
						'left' => 250.0,
					],
					[
						'due_date' => $dueDateForThirdInstalment,
						'amount' => 250.0,
						'left' => 250.0,
					],
				],
				[
					'due_date' => $dueDateForSecondInstalment,
					'amount' => 250.0,
					'left' => 250.0,
				]
			],
			[
				1000.0,
				750.0,
				[
					[
						'due_date' => $dueDateForFirstInstalment,
						'amount' => 500.0,
						'paid_amount' => 500.0,
						'order_number' => 1,
					],
					[
						'due_date' => $dueDateForSecondInstalment,
						'amount' => 250.0,
						'paid_amount' => 250.0,
						'order_number' => 2,
					],
					[
						'due_date' => $dueDateForThirdInstalment,
						'amount' => 250.0,
						'paid_amount' => 0.0,
						'order_number' => 3,
					],
				],
				250.0,
				[
					[
						'due_date' => $dueDateForFirstInstalment,
						'amount' => 500.0,
						'left' => 0.0,
					],
					[
						'due_date' => $dueDateForSecondInstalment,
						'amount' => 250.0,
						'left' => 0.0,
					],
					[
						'due_date' => $dueDateForThirdInstalment,
						'amount' => 250.0,
						'left' => 250.0,
					],
				],
				[
					'due_date' => $dueDateForThirdInstalment,
					'amount' => 250.0,
					'left' => 250.0,
				],
			],
			[
				1000.0,
				250.0,
				[
					[
						'due_date' => $dueDateForFirstInstalment,
						'amount' => 500.0,
						'paid_amount' => 250.0,
						'order_number' => 1,
					],
					[
						'due_date' => $dueDateForSecondInstalment,
						'amount' => 250.0,
						'paid_amount' => 0.0,
						'order_number' => 2,
					],
					[
						'due_date' => $dueDateForThirdInstalment,
						'amount' => 250.0,
						'paid_amount' => 0.0,
						'order_number' => 3,
					],
				],
				750.0,
				[
					[
						'due_date' => $dueDateForFirstInstalment,
						'amount' => 500.0,
						'left' => 250.0,
					],
					[
						'due_date' => $dueDateForSecondInstalment,
						'amount' => 250.0,
						'left' => 250.0,
					],
					[
						'due_date' => $dueDateForThirdInstalment,
						'amount' => 250.0,
						'left' => 250.0,
					],
				],
				[
					'due_date' => $dueDateForFirstInstalment,
					'amount' => 500.0,
					'left' => 250.0,
				]
			],
		];
	}

	private function getDueDateForSecondInstalment(): Carbon
	{
		return Carbon::createFromDate(2018, 11, 20);
	}

	private function getDueDateForThirdInstalment(): Carbon
	{
		return Carbon::createFromDate(2018, 12, 20);
	}
}
