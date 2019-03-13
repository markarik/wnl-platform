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
				'end_date' => null,
			],
			[
				'product_id' => 8,
				'payment_method_id' => 2,
				'end_date' => null,
			],
			[
				'product_id' => 8,
				'payment_method_id' => 3,
				'end_date' => '2018-05-22'
			],
			// Online course
			[
				'product_id' => 10,
				'payment_method_id' => 1,
				'end_date' => null,
			],
			[
				'product_id' => 10,
				'payment_method_id' => 2,
				'end_date' => null,
			],
			[
				'product_id' => 10,
				'payment_method_id' => 3,
				'end_date' => '2018-05-22'
			],
		]);
    }
}
