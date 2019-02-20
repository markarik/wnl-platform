<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserCourseProgress;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class ArchiveCourseProgress
{
	use Dispatchable;

	protected $user;
	protected $progress;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @param UserCourseProgress[]|Collection $progress
	 */
	public function __construct($user, $progress)
	{
		$this->user = $user;
		$this->progress = $progress;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		\DB::table('archived_user_course_progress')->insert([
			'user_id' => $this->user->id,
			'data'    => $this->progress->toJson(),
			'created_at' => Carbon::now()
		]);
	}
}
