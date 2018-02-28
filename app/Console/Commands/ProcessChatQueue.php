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
					if ($data) {
						$room = ChatRoom::find($data->room);
						if (empty($room)) {
							$room = ChatRoom::ofName($data->room)->first();
						}
						$room->messages()->create([
							'user_id' => $data->message->user_id,
							'content' => $data->message->content,
							'time'    => $data->message->time,
						]);

						$chatRoomUser = ChatRoomUser
							::where('chat_room_id', $data->room)
							->where('user_id', '<>', $data->message->user_id)
							->increment('unread_count');
					}
					break;

				case 'chatEvents:roomRead':
					$chatRoomUser = ChatRoomUser
						::where('chat_room_id', $data->room)
						->where('user_id', $data->user_id)
						->update(['unread_count' => 0]);
					break;
			}


			// TODO: What should be done if message format is invalid ??
			$resolver->acknowledge($payload);
		});
	}
}
