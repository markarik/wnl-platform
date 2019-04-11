<?php

use App\Models\Product;
use App\Models\ProductInstalment;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$now = Carbon\Carbon::now();
		$someday = (clone $now)->addYears(10);

		foreach (ExampleData::PRODUCTS as $product) {
			$productModel = Product::create([
				'name' => $product['name'],
				'invoice_name' => $product['invoice_name'],
				'price' => $product['price'],
				'slug' => $product['slug'],
				'quantity' => $product['quantity'],
				'initial' => $product['initial'],
				'delivery_date' => $now,
				'created_at' => $now,
				'updated_at' => $now,
				'course_start' => $now,
				'course_end' => $someday,
				'access_start' => $now,
				'access_end' => $someday,
				'signups_start' => $now,
				'signups_end' => $someday,
				'signups_close' => $someday,
			]);

			if ($product['has_instalments']) {
				ProductInstalment::create([
					'product_id' => $productModel->id,
					'value_type' => 'percentage',
					'value' => '50',
					'due_days' => 7,
					'order_number' => 1,
				]);

				ProductInstalment::create([
					'product_id' => $productModel->id,
					'value_type' => 'percentage',
					'value' => '50',
					'due_date' => (clone $now)->addMonths(1),
					'order_number' => 2,
				]);

				ProductInstalment::create([
					'product_id' => $productModel->id,
					'value_type' => 'percentage',
					'value' => '100',
					'due_date' => (clone $now)->addMonths(2),
					'order_number' => 3,
				]);
			}
		}
	}
}
