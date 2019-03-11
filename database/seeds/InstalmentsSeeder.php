<?php

use Illuminate\Database\Seeder;

class InstalmentsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$online = \App\Models\Product::slug('wnl-online');
		$album = \App\Models\Product::slug('wnl-album');
		$now = Carbon\Carbon::now();

		DB::table('product_instalments')->insert([
			[
				'product_id' => $online->id,
				'value_type' => 'percentage',
				'value' => 50,
				'due_days' => 7,
				'due_date' => null,
				'order_number' => 1,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'product_id' => $online->id,
				'value_type' => 'percentage',
				'value' => 50,
				'due_days' => null,
				'due_date' => '2018-06-20',
				'order_number' => 2,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'product_id' => $online->id,
				'value_type' => 'percentage',
				'value' => 100,
				'due_days' => null,
				'due_date' => '2018-07-20',
				'order_number' => 3,
				'created_at' => $now,
				'updated_at' => $now,
			],

			[
				'product_id' => $album->id,
				'value_type' => 'percentage',
				'value' => 50,
				'due_days' => 7,
				'due_date' => null,
				'order_number' => 1,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'product_id' => $album->id,
				'value_type' => 'percentage',
				'value' => 50,
				'due_days' => null,
				'due_date' => '2018-06-20',
				'order_number' => 2,
				'created_at' => $now,
				'updated_at' => $now,
			],
			[
				'product_id' => $album->id,
				'value_type' => 'percentage',
				'value' => 100,
				'due_days' => null,
				'due_date' => '2018-07-20',
				'order_number' => 3,
				'created_at' => $now,
				'updated_at' => $now,
			],
		]);
	}
}
