<?php

namespace App\Notifications;

use App\Models\Product;
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
			->to('#wnl-platforma')
			->content(str_repeat($this->parrot(), 7))
			->attachment(function ($attachment) {
				$attachment->title('Zamówienie #' . $this->order->id, url("admin/app/users/{$this->order->user->id}#orders"))
					->fields([
						'Wariant'          => $this->order->product->name,
						'Od'               => $this->order->user->full_name,
						'Pozostało miejsc' => $this->order->product->quantity . ' z ' .$this->order->product->initial,
						'Metoda płatności' => $this->order->method,
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

	protected function parrot()
	{
		return array_random([
			':reverse-conga-parrot:',
			':aussie-conga-line-parrot:',
			':aussie-parrot:',
			':deal-with-it-parrot:',
			':explody-parrot:',
			':fast-parrot:',
			':love-parrot:',
			':middle-parrot-hd:',
			':margarita-parrot:',
			':nyan-parrot:',
			':parrot:',
			':parrot-mustache:',
			':shipit-parrot:',
			':thumbs-up-parrot:',
			':triplets-parrot:',
			':twins-parrot:',
		]);
	}
}
