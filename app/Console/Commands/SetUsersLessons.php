<?php

namespace App\Console\Commands;

use App\Models\LessonAvailability;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Console\Command;

class SetUsersLessons extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:set-user-lessons';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate data from lessons_availabilities table and attach the lesson\'s avalability with a user';

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
		$users = User::all();
		$lessonAvalabilities = LessonAvailability::all();
		$bar = $this->output->createProgressBar($users->count());

		foreach($users as $user) {
			foreach($lessonAvalabilities as $lesson) {
				if ($user->isAdmin() || $user->hasRole('moderator')) {
					\DB::table('user_lesson')->insert([
						'user_id' => $user->id,
						'lesson_id' => $lesson->lesson_id,
						'start_date' => $lesson->start_date
					]);
				} else if ($user->hasRole('edition-2-participant')) {
					$lessonAccess = \DB::table('lesson_user_access')
						->where('lesson_id', $lesson->lesson_id)
						->where('user_id', $user->id)
						->first();
					if (is_null($lessonAccess)) {
						if ($lesson->lesson_id === 17 && !$user->hasRole('workshop-participant')) {
							continue;
						} else {
							\DB::table('user_lesson')->insert([
								'user_id' => $user->id,
								'lesson_id' => $lesson->lesson_id,
								'start_date' => $lesson->start_date
							]);
						}
					} else if (!empty($lessonAccess->access)) {
						\DB::table('user_lesson')->insert([
							'user_id' => $user->id,
							'lesson_id' => $lesson->lesson_id,
							'start_date' => $lesson->start_date
						]);
					}
				}
			}
			$bar->advance();
		}
		$bar->finish();
		print "\n";
		return true;
	}
}
