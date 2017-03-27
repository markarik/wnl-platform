<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CouponsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('coupons')->insert([
			'name'       => 'Zniżka dla subskrybentów newslettera',
			'slug'       => 'subscriber-coupon',
			'value'      => '200',
			'type'       => 'amount',
			'expires_at' => Carbon::parse('first day of June 2017'),
		]);
	}
}
