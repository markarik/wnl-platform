<?php

namespace App\Console\Commands;

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
		\Rabbit::consume('chat-events', function ($payload, $resolver) {

			$data = json_decode($payload->body);

			if (empty($data)) {
				return $resolver->acknowledge($payload);
			}

			$event = $data->event;

			switch ($event) {
				case 'chatEvents:messageSend':
					$this->handleMessageSend($data);
					break;

				case 'chatEvents:roomRead':
					$this->handleMarkRoomAsRead($data);
					break;
			}

			// TODO: What should be done if message format is invalid ??
			$resolver->acknowledge($payload);
		});
	}

	private function handleMessageSend($messagePayload) {
		$room = ChatRoom::find($messagePayload->room);
		if (empty($room)) {
			$room = ChatRoom::ofName($messagePayload->room)->first();
		}
		$room->messages()->create([
			'user_id' => $messagePayload->message->user_id,
			'content' => $messagePayload->message->content,
			'time'    => $messagePayload->message->time,
		]);

		$chatRoomUser = ChatRoomUser
			::where('chat_room_id', $messagePayload->room)
			->where('user_id', '<>', $messagePayload->message->user_id)
			->increment('unread_count');
	}

	private function handleMarkRoomAsRead($eventPayload) {
		$chatRoomUser = ChatRoomUser
			::where('chat_room_id', $eventPayload->room)
			->where('user_id', $eventPayload->user_id)
			->update(['unread_count' => 0]);
	}
}
