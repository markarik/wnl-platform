<?php

use Illuminate\Database\Seeder;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('payment_methods')->insert([
			['slug' => 'transfer'],
			['slug' => 'online'],
			['slug' => 'instalments'],
		]);

		DB::table('payment_method_product')->insert([
			// Album
			[
				'product_id' => 8,
				'payment_method_id' => 1,
			],
			[
				'product_id' => 8,
				'payment_method_id' => 2,

			],
			// Online course + workshops
			[
				'product_id' => 9,
				'payment_method_id' => 1,
			],
			[
				'product_id' => 9,
				'payment_method_id' => 2,
			],
			[
				'product_id' => 9,
				'payment_method_id' => 3,
				'end_date' => '2018-06-09',
			],
			// Online course
			[
				'product_id' => 10,
				'payment_method_id' => 1,
			],
			[
				'product_id' => 10,
				'payment_method_id' => 2,
			],
			[
				'product_id' => 10,
				'payment_method_id' => 3,
				'end_date' => '2018-06-09'
			],
		]);
    }
}
