<?php

namespace Tests\Unit\Http\Controllers\Api\Serializer;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Tests\TestCase;

class OrderTest extends TestCase
{
	/**
	 * @dataProvider getInstalmentsAttributeDataProvider
	 * @param float $price
	 * @param float $paidAmount
	 * @param float $expectedTotalLeft
	 * @param array $expectedInstalments
	 * @param array $expectedNextPayment
	 */
	public function testGetInstalmentsAttributeNotPaid($price, $paidAmount, $expectedTotalLeft, $expectedInstalments, $expectedNextPayment) {
		$order = $this->makeOrder($price, $paidAmount);

		$instalments = $order->instalments['instalments'];
		$this->assertCount(3, $instalments);

		for ($i = 0; $i < count($instalments); $i++) {
			$this->assertInstalmentData(
				$expectedInstalments[$i]['dueDate'],
				$expectedInstalments[$i]['amount'],
				$expectedInstalments[$i]['left'],
				$instalments[$i]
			);
		}

		$nextPayment = $order->instalments['nextPayment'];

		$this->assertTrue($expectedNextPayment['dueDate']->isSameDay($nextPayment['date']));
		$this->assertEquals($expectedNextPayment['amount'], $nextPayment['amount']);

		$this->assertEquals(false, $order->instalments['allPaid']);
		$this->assertEquals($expectedTotalLeft, $order->instalments['total']);
	}

	private function makeOrder(float $price, float $paidAmount): Order
	{
		$product = factory(Product::class)->make([
			'price' => $price
		]);

		$order = factory(Order::class)->make([
			'created_at' => Carbon::now(),
			'paid_amount' => $paidAmount,
			'product_id' => null
		]);

		$order->setRelation('product', $product);

		return $order;
	}

	private function assertInstalmentData(Carbon $expectedDate, float $expectedAmount, float $expectedLeft, array $instalment): void
	{
		$this->assertTrue($expectedDate->isSameDay($instalment['date']));
		$this->assertEquals($expectedAmount, $instalment['amount']);
		$this->assertEquals($expectedLeft, $instalment['left']);
	}

	public function testGetInstalmentsAttributeAllPaid() {
		$order = $this->makeOrder(1000, 1000);

		$this->assertEquals([
			'allPaid' => true,
			'instalments' => []
		], $order->instalments);
	}

	public function getInstalmentsAttributeDataProvider()
	{
		$date0 = Carbon::now()->addDays(7);
		$date1 = Carbon::createFromDate(2018, 11, 20);
		$date2 = Carbon::createFromDate(2018, 12, 20);

		return [
			[
				1000.0,
				500.0,
				500.0,
				[
					[
						'dueDate' => $date0,
						'amount' => 500.0,
						'left' => 0.0,
					],
					[
						'dueDate' => $date1,
						'amount' => 250.0,
						'left' => 250.0,
					],
					[
						'dueDate' => $date2,
						'amount' => 250.0,
						'left' => 250.0,
					],
				],
				[
					'dueDate' => $date1,
					'amount' => 250.0,
					'left' => 250.0,
				]
			],
			[
				1000.0,
				750.0,
				250.0,
				[
					[
						'dueDate' => $date0,
						'amount' => 500.0,
						'left' => 0.0,
					],
					[
						'dueDate' => $date1,
						'amount' => 250.0,
						'left' => 0.0,
					],
					[
						'dueDate' => $date2,
						'amount' => 250.0,
						'left' => 250.0,
					],
				],
				[
					'dueDate' => $date2,
					'amount' => 250.0,
					'left' => 250.0,
				],
			],
			[
				1000.0,
				250.0,
				750.0,
				[
					[
						'dueDate' => $date0,
						'amount' => 500.0,
						'left' => 250.0,
					],
					[
						'dueDate' => $date1,
						'amount' => 250.0,
						'left' => 250.0,
					],
					[
						'dueDate' => $date2,
						'amount' => 250.0,
						'left' => 250.0,
					],
				],
				[
					'dueDate' => $date0,
					'amount' => 250.0,
					'left' => 250.0,
				]
			],
		];
	}
}
