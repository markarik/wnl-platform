<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderCreated extends Notification implements ShouldQueue
{
	use Queueable;
	protected $order;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($order)
	{
		$this->order = $order;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed $notifiable
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
	 * @return SlackMessage
	 */
	public function toSlack($notifiable)
	{
		return (new SlackMessage)
			->to('#wnl-platforma')
			->content(str_repeat(':reverse-conga-parrot:', 7))
			->attachment(function ($attachment) {
				$attachment->title('Zamówienie #' . $this->order->id)
					->fields([
						'Wariant' => $this->order->product->name,
					]);
			});
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}
