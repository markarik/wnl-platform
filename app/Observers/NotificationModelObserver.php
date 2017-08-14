<?php

namespace App\Observers;


use App\Jobs\ClearModelsNotifications;
use Illuminate\Foundation\Bus\DispatchesJobs;

class NotificationModelObserver
{
	use DispatchesJobs;

	public function deleted($model)
	{
		$this->dispatch(new ClearModelsNotifications($model));
	}
}
