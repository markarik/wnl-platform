<?php

namespace App\Observers;

use App\Models\Profile;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ProfileObserver
{
	use DispatchesJobs;

	public function created(Profile $userProfile)
	{
		$userProfile->searchable();
	}

	public function updated(Profile $userProfile)
	{
		$userProfile->searchable();
	}

	public function deleted(Profile $userProfile)
	{
		$userProfile->unsearchable();
	}
}
