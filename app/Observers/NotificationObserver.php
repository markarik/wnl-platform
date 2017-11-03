<?php namespace App\Observers;


use App\Models\Notification;
use Illuminate\Foundation\Bus\DispatchesJobs;


class NotificationObserver
{
	use DispatchesJobs;

	public function updated(Notification $notification)
	{

	}
}
