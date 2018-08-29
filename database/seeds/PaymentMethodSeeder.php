<?php

use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();

        foreach (ExampleData::PAYMENT_METHODS as $method) {
            DB::table('payment_methods')->insert([
                'slug' => $method['slug'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->bindPaymentMethodsToProducts();
    }

    private function bindPaymentMethodsToProducts()
    {
        $now = \Carbon\Carbon::now();
        $someday = (clone $now)->addYears(10);
        $products = \DB::table('products')->select(['id'])->get();
        $payment_methods = \DB::table('payment_methods')->select(['id'])->get();
        foreach ($products as $product) {
            foreach ($payment_methods as $payment_method) {
                \DB::table('payment_method_product')->insert([
                    [
                        'product_id' => $product->id,
                        'payment_method_id' => $payment_method->id,
                        'start_date' => $now,
                        'end_date' => $someday,
                    ]
                ]);
            }
        }
    }
}
