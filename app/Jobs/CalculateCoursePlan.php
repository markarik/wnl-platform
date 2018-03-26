<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserLessonAvailability;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class CalculateCoursePlan implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $user;
	protected $lessons;
	protected $startDate;
	protected $endDate;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @param array $lessons
	 * @param Carbon $startDate
	 * @param Carbon $endDate
	 */
	public function __construct($user, $lessons, $startDate, $endDate)
	{
		$this->user = $user;
		$this->lessons = $lessons;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{

	}
}
