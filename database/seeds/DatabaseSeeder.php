<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$this->call( UsersTableSeeder::class );
		$this->call( ModulesTableSeeder::class );
		$this->call( ChaptersTableSeeder::class );
		$this->call( SectionsTableSeeder::class );
		$this->call( ProductsTableSeeder::class );
	}
}
