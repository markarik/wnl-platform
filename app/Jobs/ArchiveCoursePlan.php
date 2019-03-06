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
	protected $shouldClear;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @param bool $shouldClear
	 */
	public function __construct($user, $shouldClear = true)
	{
		$this->user = $user;
		$this->shouldClear = $shouldClear;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 * @throws \Exception
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

		if ($this->shouldClear) {
			UserLesson::where('user_id', $this->user->id)->delete();
		}
	}
}
