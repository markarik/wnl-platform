<?php

namespace App\Console\Commands;

use App\Models\LessonAvailability;
use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MigrateUserLessonAvailabilities extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'users:set-lessons-availabilities';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate data from lessons_availabilities table and attach the avalability with user';

	/**
	 * Create a new command instance.
	 *
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
		$inserted = 0;
		$modsInserted = 0;
		$usersInserted = 0;
		$users = User::all();
		$lessonAvalabilities = LessonAvailability::all();

		foreach($users as $user) {
			foreach($lessonAvalabilities as $lesson) {
				if ($user->isAdmin() || $user->hasRole('moderator')) {
					$modsInserted++;
					\DB::table('user_lesson_availabilities')->insert([
						'user_id' => $user->id,
						'lesson_id' => $lesson->lesson_id,
						'start_date' => $lesson->start_date
					]);
				} else if ($user->hasRole('edition-2-participant')) {
					if ($user->lessonsAccess->isEmpty()) {
						if ($lesson->lesson_id === 17 && !$user->hasRole('workshop-participant')) {
							continue;
						} else {
							$usersInserted++;
							\DB::table('user_lesson_availabilities')->insert([
								'user_id' => $user->id,
								'lesson_id' => $lesson->lesson_id,
								'start_date' => $lesson->start_date
							]);
						}
					} else {
						$lessonAccess = Lesson::find($lesson->lesson_id)->userAccess->where('user_id', $user->id)->first();
						$hasAccess = !is_null($lessonAccess) && $lessonAccess->access;
						if ($hasAccess) {
							$inserted++;
							\DB::table('user_lesson_availabilities')->insert([
								'user_id' => $user->id,
								'lesson_id' => $lesson->lesson_id,
								'start_date' => $lesson->start_date
							]);
						}
					}
				}
			}
		}

		print $inserted;
		print "\n";
		print $modsInserted;
		print "\n";
		print $usersInserted;
		print "\n";
		return true;
	}
}
