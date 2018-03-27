<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;


class PopulateUserCoursePlan implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $user;
	protected $product;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @param $product
	 */
	public function __construct($user, $product)
	{
		$this->user = $user;
		$this->product = $product;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		dispatch_now(new ArchiveCoursePlan($this->user));

		$availabilities = [];

		foreach ($this->product->lessons as $lesson) {
			$availabilities[] = [
				'user_id' => $this->user->id,
				'lesson_id' => $lesson->id,
				'start_date' => $lesson->pivot->start_date,
			];
		}

		\DB::table('user_lesson_availabilities')->insert($availabilities);
	}
}
