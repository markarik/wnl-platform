<?php

namespace App\Console\Commands;

use App\Models\ChatRoom;
use App\Models\Lesson;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class MigrateChatRooms extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'chat:migrate-rooms';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Assign chat room types basing on their name.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$rooms = ChatRoom::all();
		$lessonAccess = Permission::slug('lesson_access');
		$roleAccess = Permission::slug('role_access');
		$moderatorsRole = Role::byName('moderator');

		foreach ($rooms as $room) {
			if (!str_is('private-*', $room->name)) {
				$room->type = 'public';
				$room->slug = $room->name;
				$room->save();
			}

			if (str_is('*lessons-*', $room->name)) {
				$lessonAccess->chatRooms()->syncWithoutDetaching($room);
				$lessonId = array_pop(explode('-', $room->name));
				$lesson = Lesson::find($lessonId);
				if ($lesson) {
					$room->lessons()->syncWithoutDetaching($lesson);
				} else {
					$this->warn("Couldn't find lesson of ID {$lessonId}");
				}
			}

			if (str_is('moderatorzy', $room->name)) {
				$roleAccess->chatRooms()->syncWithoutDetaching($room);
				$room->roles()->syncWithoutDetaching($moderatorsRole);
			}
		}

		return;
	}
}
