<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserCourseProgress;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class ResetSatisfactionGuaranteeStatus
{
	use Dispatchable;

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
		$this->user->has_finished_entry_exam = false;
		$this->user->save;

		\Log::debug('IM HERE>>>>>>>>>>>');
		$this->user->settings->patch([
			'skip_satisfaction_guarantee_modal' => true
		]);
	}
}
