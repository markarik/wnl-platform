<?php

namespace Tests\Api\User;

use App\Models\User;
use App\Models\Lesson;
use App\Models\UserLesson;
use Tests\Api\ApiTestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserLessonTest extends ApiTestCase
{
    use DatabaseTransactions;

    /** @test * */
    public function openAllLessons()
    {
        $user = factory(User::class)->create();
        $lessons = factory(Lesson::class, 10)->create();

        foreach ($lessons as $lesson) {
            factory(UserLesson::class)->create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'start_date' => Carbon::now()->subDays(100)
            ]);
        }

        $this
            ->actingAs($user)
            ->json('PUT', $this->url("/user_lesson/$user->id"), [
                'work_load' => 0,
                'start_date' => Carbon::now()->toDateString(),
                'user_id' => $user->id,
                'work_days' => ['1,2,5']
            ]);

        foreach($user->lessonsAvailability as $lesson) {
            $this->assertTrue($lesson->startDate($user)->isToday(), "Start date is not today");
        };
    }
}