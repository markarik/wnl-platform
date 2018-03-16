<?php

namespace App\Observers;

use App\Models\UserProfile;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UserProfileObserver
{
	use DispatchesJobs;

	public function created(UserProfile $userProfile)
	{
		$userProfile->searchable();
	}

	public function updated(UserProfile $userProfile)
	{
		$userProfile->searchable();
	}

	public function deleted(UserProfile $userProfile)
	{
		$userProfile->unsearchable();
	}
}
