<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserLesson;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;


class ArchiveCoursePlan
{
	use Dispatchable, SerializesModels;

	protected $user;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 */
	public function __construct($user)
	{
		$this->user = $user;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$plan = UserLesson::where('user_id', $this->user->id)
			->get()
			->map(function ($model) {
				return array_except($model->toArray(), ['id', 'updated_at']);
			});

		\DB::table('archived_user_course_plans')->insert([
			'user_id' => $this->user->id,
			'data'    => $plan->toJson(),
		]);

		UserLesson::where('user_id', $this->user->id)->delete();
	}
}
