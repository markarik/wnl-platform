<?php

namespace App\Console\Commands;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Console\Command;

class ProcessChatQueue extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'chat:process-queue';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Consume messages queue with chat events';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		\Rabbit::consume('chat-events', function ($message, $resolver) {

			$data = json_decode($message->body);

			if ($data) {
				$room = ChatRoom::find($data->room);
				if (empty($room)) {
					$room = ChatRoom::ofName($data->room)->first();
				}
				if ($this->validate($data, $message, $resolver)) {
					$chatMessage = $room->messages()->create([
						'user_id' => $data->message->user_id,
						'content' => $data->message->content,
						'time'    => $data->message->time,
					]);

					if ($chatMessage) {
						$this->acceptMessage($message, $resolver);
					} else {
						$this->rejectMessage($message, 'Unable to persist.');
					}
				}
			} else {
				$this->rejectMessage($message, 'Invalid JSON');
			}
		});

		return;
	}

	protected function validate($data, $message, $resolver)
	{
		$length = strlen($data->message->content);
		if ($length > ChatMessage::MAX_MSG_CONTENT_LEN) {
			// Validation has to be done right by client and
			// live messaging server. If some invalid message
			// reaches this point, we're just skipping it.
			$this->dropMessage($message, $resolver, "Message content too long.");

			return false;
		}

		return true;
	}

	protected function dropMessage($message, $resolver, $reason)
	{
		\Log::warning("Queue message dropped! Reason: {$reason}");

		$resolver->acknowledge($message);
	}

	protected function acceptMessage($message, $resolver)
	{
		$resolver->acknowledge($message);
	}

	protected function rejectMessage($message, $reason)
	{
		\Log::error("Queue message rejected! Reason: {$reason}");
	}
}
