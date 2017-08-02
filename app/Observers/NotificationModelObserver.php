<?php

namespace App\Observers;

use App\Models\Notification;

class NotificationModelObserver
{
	public function deleted($model)
	{
		$this->updateNotifications($model);
	}


	public function updateNotifications($model)
	{
		 $notifications = \DB::table('notifications')
				 ->whereRaw('data->"$.subject.id" = ' . $model->id)
				 ->whereRaw('data->"$.subject.type" = "' . snake_case(class_basename($model)) . '"')
				 ->get();

		$notificationsIds = $notifications->pluck('id')->toArray();

		\DB::listen(function ($query) {
			dump($query->sql);
		});

		\DB::table('notifications')
			// ->update(['data->context' => '', 'data->referer' => '', 'data->subject->text' => '', 'data->deleted' => true])
			->whereIn('id', $notificationsIds)
			->delete();

		foreach($notifications as $notification) {
			$notification->data->deleted = true;
			$notification->data->referer = '';
			$notification->data->context = '';
			$notification->data->subject->text = '';
			$this->pushLive($notification);
		}
	}

	public function pushLive($notification)
	{
		$event = new \stdClass();
		$event->data = $notification->data;
		$event->id = $notification->event_id;
		$event->data['read_at'] = $notification->read_at->timestamp ?? null;
		$event->data['seen_at'] = $notification->seen_at->timestamp ?? null;

		$user = User::find($notification->notifiable_id);

		$newNotification = new EventNotification($event, $notification->channel);
		$newNotification->id = $notification->id;
		$newNotification->event = $event;
		Notify::sendNow($user, $newNotification, [LiveChannel::class]);
	}
}
