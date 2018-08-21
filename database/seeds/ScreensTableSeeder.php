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
        DB::table('screens')->insert([
            'type' => 'quiz',
            'content' => null,
            'name' => 'Pytania kontrolne',
            'lesson_id' => 1,
            'meta' => '{"resources": [{"id": 1, "name": "quiz_sets"}]}',
            'order_number' => 1
        ]);
    }
}
