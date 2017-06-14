<?php
use Illuminate\Database\Seeder;
class ScreensTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		for ($i = 1; $i < 15; $i++) {
			DB::table('screens')->insert([
				'type'      => 'quiz',
				'content'   => null,
				'name'      => 'Pytania kontrolne',
				'lesson_id' => $i,
				'meta'      => '{"resources": [{"id": ' . $i . ', "name": "quiz_sets"}]}',
				'order_number' => 1
			]);
		}
	}
}
