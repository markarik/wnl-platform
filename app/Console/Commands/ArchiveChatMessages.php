<?php

namespace App\Console\Commands;

use DB;
use App\Models\ChatRoom;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class ArchiveChatMessages extends Command
{
	const ROOM_MESSAGES_KEY = 'room-messages-';

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'chat:archive-messages';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Move old chat messages to MySQL DB';

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

//		foreach ($this->getRooms() as $room) {
//			$room = ChatRoom::firstOrCreate(['name' => $room]);
//		}

		$this->getMessages();

		DB::transaction(function () {
		});
	}

	/**
	 * Search for chat rooms in redis DB.
	 *
	 * @param $cursor
	 * @param int $limit
	 * @return mixed
	 */
	protected function scan($cursor, $limit = 1000)
	{
		return Redis::scan($cursor, 'MATCH', self::ROOM_MESSAGES_KEY . '*', 'COUNT', $limit);
	}

	/**
	 * Get list of chat rooms names.
	 *
	 * @return array
	 */
	protected function getRooms()
	{
		$cursor = null;
		$rooms = [];

		// Redis indicates the beginning,
		// as well as the end of the scan results list,
		// by the cursor equal to "0".
		while ($cursor !== 0) {
			list ($cursor, $results) = $this->scan($cursor);
			$cursor = (int)$cursor;
			$rooms = array_merge($rooms, $results);
		}

		$roomsNames = array_map(function ($item) {
			return str_replace(self::ROOM_MESSAGES_KEY, '', $item);
		}, $rooms);

		return $roomsNames;
	}

	protected function getMessages($leave = 200, $take = 100)
	{
		$messages = Redis::lrange('room-messages-courses-1', -1 * ($take + $leave), -1 * ($leave + 1));
		dd($messages);
	}
}
