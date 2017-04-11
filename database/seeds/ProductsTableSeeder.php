<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('products')->insert([
			'name'         => 'Kurs stacjonarny',
			'invoice_name' => 'Dostęp do platformy e-learningowej od 17 czerwca 2017r. do 31 października 2017r., oraz udział w 6 spotkaniach warsztatowych w ramach 1 edycji kursu "Więcej niż LEK", przygotowującego do Lekarskiego Egzaminu Końcowego',
			'price'        => 2200.00,
			'slug'         => 'wnl-online-onsite',
			'quantity'     => 198,
		]);

		DB::table('products')->insert([
			'name'         => 'Kurs internetowy',
			'invoice_name' => 'Dostęp do platformy e-learningowej od 17 czerwca 2017r. do 31 października 2017r. w ramach 1 edycji kursu "Więcej niż LEK", przygotowującego do Lekarskiego Egzaminu Końcowego',
			'price'        => 1500.00,
			'slug'         => 'wnl-online',
			'quantity'     => 97,
		]);
	}
}
