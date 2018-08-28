<?php


namespace App\Observers;


use App\Models\ChatRoom;
use App\Models\Permission;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Lesson;


class LessonObserver
{
	use DispatchesJobs;


	public function created(Lesson $lesson)
	{

	}

	protected function createLessonChat($lesson)
	{
		$chatRoom = ChatRoom::create([
			'type' => 'public',
			'slug' => "courses-1-lessons-{$lesson->id}",
		]);
		$lessonAccess = Permission::slug('lesson_access');
		$lessonAccess->chatRooms()->syncWithoutDetaching($chatRoom);
		$chatRoom->lessons()->syncWithoutDetaching($lesson);
	}
}
