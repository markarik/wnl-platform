<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Foundation\Bus\Dispatchable;


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
		$this->user->save();

		$currentSettings = $this->user->settings->settings ?? [];
		$updatedSettings = array_merge($currentSettings, [
			UserSettings::SETTING_SKIP_SATISFACTION_GUARANTEE_MODAL => false
		]);
		$this->user->settings()->updateOrCreate(
			['user_id' => $this->user->id],
			['settings' => $updatedSettings]
		);
	}
}
