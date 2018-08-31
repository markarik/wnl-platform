<?php

use Illuminate\Database\Seeder;

class CoursePlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \DB::table('users')->select(['id'])->get();
        $lessons = \DB::table('lessons')->select(['id'])->get();
        $now = Carbon\Carbon::now();
        $availabilities = [];

        foreach ($users as $user) {
            foreach ($lessons as $lesson) {
                $availabilities[] = [
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                    'start_date' => $now,
                    'created_at' => $now,
                ];
            }
        }

        \DB::table('user_lesson')->insert($availabilities);
    }
}
