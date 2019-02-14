<?php

namespace App\Console\Commands;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatRoomUser;
use Illuminate\Console\Command;

class ProcessChatQueue extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'chat:process-queue {--debug}';

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
		$this->info('Started.');

		\Rabbit::consume('chat-events', function ($payload, $resolver) {

			if ($this->option('debug')) {
				$this->info('New message.');
			}

			$data = json_decode($payload->body);

			if (!$data) {
				$this->rejectPayload($payload, $resolver, 'Invalid payload!');

				return false;
			}

			switch ($data->event) {
				case 'chatEvents:sendMessage':
					$this->handleMessageSend($data, $payload, $resolver);
					break;

				case 'chatEvents:markRoomAsRead':
					$this->handleMarkRoomAsRead($data, $payload, $resolver);
					break;

				default:
					$this->dropPayload($payload, $resolver, 'Invalid event!');
			}
		}, [
			'persistent' => true
		]);

		$this->info('Exiting.');

		return;
	}

	private function handleMessageSend($data, $payload, $resolver)
	{
		if (!$this->validateMessage($data, $payload, $resolver)) {
			return false;
		}

		$roomId = $data->room->id;
		$room = ChatRoom::find($roomId);

		$chatMessage = $room->messages()->create([
			'user_id' => $data->message->user_id,
			'content' => $data->message->content,
			'time'    => $data->message->time,
		]);

		if (!$chatMessage) {
			$this->rejectPayload($payload, $resolver, 'Unable to persist message!');

			return false;
		}

		$this->acceptPayload($payload, $resolver);

		$builder = ChatRoomUser
			::where('chat_room_id', $roomId)
			->where('user_id', '<>', $data->message->user_id);
		(clone $builder)->increment('unread_count');
		(clone $builder)->update(['log_pointer' => $data->message->time]);

		return true;
	}

	protected function handleMarkRoomAsRead($data, $payload, $resolver)
	{
		$roomId = $data->room->id;
		$chatRoomUser = ChatRoomUser
			::where('chat_room_id', $roomId)
			->where('user_id', $data->user_id)
			->update([
				'unread_count' => 0,
				'log_pointer'  => $data->time,
			]);

		$this->acceptPayload($payload, $resolver);
	}

	protected function validateMessage($data, $payload, $resolver)
	{
		$length = strlen($data->message->content);
		if ($length > ChatMessage::MAX_MSG_CONTENT_LEN) {
			// Validation has to be done right by client and
			// live messaging server. If some invalid message
			// reaches this point, we're just skipping it.
			$this->dropPayload($payload, $resolver, "Message content too long.");

			return false;
		}

		return true;
	}

	protected function dropPayload($payload, $resolver, $reason)
	{
		$errorMessage = "Queue message dropped! Reason: {$reason}";
		\Log::warning($errorMessage);
		$this->warn($errorMessage);

		$resolver->acknowledge($payload);

		return;
	}

	protected function acceptPayload($payload, $resolver)
	{
		$resolver->acknowledge($payload);
	}

	protected function rejectPayload($payload, $resolver, $reason)
	{
		$errorMessage = "Queue message rejected! Reason: {$reason}";
		\Log::error($errorMessage);
		$this->error($errorMessage);
		$resolver->reject($payload);
	}
}
