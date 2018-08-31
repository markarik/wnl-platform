<?php

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
            DB::table('products')->insert([
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
        }
    }
}
