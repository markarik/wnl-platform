<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
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
	 * @param Product $product
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
		$availabilities = [];

		foreach ($this->product->lessons as $lesson) {
			if ($lesson->isAccessible($this->user)) {
				$startDate = $lesson->startDate($this->user);
			} else {
				$startDate = $lesson->pivot->start_date;
			}
			$availabilities[] = [
				'user_id' => $this->user->id,
				'lesson_id' => $lesson->id,
				'start_date' => $startDate,
				'created_at' => Carbon::now()
			];
		}

		dispatch_now(new ArchiveCoursePlan($this->user));

		\DB::table('user_lesson')->insert($availabilities);
	}
}
