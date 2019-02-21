<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class TaskCreated extends Notification implements ShouldQueue
{
	use Queueable;
	protected $task;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($task)
	{
		$this->task = $task;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['slack'];
	}

	/**
	 * Send notification to slack channel
	 *
	 * @param mixed $notifiable
	 *
	 * @return SlackMessage
	 */
	public function toSlack($notifiable)
	{
		return (new SlackMessage)
			->success()
			->content($this->task->text)
			->attachment(function ($attachment) {
				$attachment
					->title('Jedziesz Szwagier', url('app/help/tech'));
			});
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed $notifiable
	 *
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}
