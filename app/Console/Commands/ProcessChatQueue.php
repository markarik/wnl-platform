<?php

namespace App\Console\Commands;

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
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

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
				$room->messages()->create([
					'user_id' => $data->message->user_id,
					'content' => $data->message->content,
					'time'    => $data->message->time,
				]);
			}

			// TODO: What should be done if message format is invalid ??

			$resolver->acknowledge($message);
		});
	}
}
