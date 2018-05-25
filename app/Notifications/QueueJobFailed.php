<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueJobFailed extends Notification implements ShouldQueue
{
	use Queueable;

	private $jobName;
	private $message;
	private $file;
	private $line;

	/**
	 * Create a new notification instance.
	 *
	 * @param String $jobName
	 * @param String $message
	 * @param String $file
	 * @param String $line
	 */
	public function __construct($jobName, $message, $file, $line)
	{
		$this->jobName = $jobName;
		$this->message = $message;
		$this->file = $file;
		$this->line = $line;
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
	 * @param $notifiable
	 *
	 * @return SlackMessage
	 */
	public function toSlack($notifiable)
	{
		return (new SlackMessage)
			->success()
			->to('#queue-monitoring')
			->content("Job $this->jobName failed")
			->attachment(function ($attachment) {
				$attachment->fields([
					'message' => $this->message,
					'file' => $this->file,
					'line' => $this->line
				]);
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
