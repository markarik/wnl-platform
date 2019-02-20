<?php

namespace App\Jobs;

use DB;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use App\Notifications\EventNotification;
use App\Notifications\Media\LiveChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification as Notify;

class ClearModelsNotifications implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable;

	/**
	 * @var Model $model
	 */
	private $model;

	/**
	 * Create a new job instance.
	 *
	 * @param Model $model
	 */
	public function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->updateNotifications($this->model);
	}

	public function updateNotifications($model)
	{
		$notifications = $this->getNotifications($model);
		$this->deleteNotifications($notifications);

		// subject is resolved by moderator, not deleted by an author
		if (!empty($model->deleted_at) && !$model->forceDeleting) {
			$params = ['resolved' => true];
		} else {
			$params = ['deleted' => true];
		}

		foreach ($notifications as $notification) {
			$notification->data = $this->update($notification->data, $params);
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

	private function deleteNotifications($notifications)
	{
		$notificationsIds = $notifications->pluck('id')->toArray();

		DB::table('notifications')
			->whereIn('id', $notificationsIds)
			->delete();
	}

	private function getNotifications($model)
	{
		$model->resource = snake_case(class_basename($model));

		// Officially craziest eloquent query:
		return Notification::select()
			->where(function ($query) use ($model) {
				$query
					->whereRaw('data->"$.subject.id" = ' . $model->id)
					->whereRaw('data->"$.subject.type" = "' . $model->resource . '"');
			})
			->orWhere(function ($query) use ($model) {
				$query
					->whereRaw('data->"$.objects.id" = ' . $model->id)
					->whereRaw('data->"$.objects.type" = "' . $model->resource . '"');
			})
			->get();
	}

	private function update($data, $params)
	{
		$deletedNotificationParams = [
			'referer' => '',
			'context' => '',
			'subject' => ['text' => '']
		];

		return array_merge($data, $deletedNotificationParams, $params);
	}
}
