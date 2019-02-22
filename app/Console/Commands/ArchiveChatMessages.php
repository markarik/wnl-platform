<?php

namespace App\Console\Commands;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Closure;
use DB;
use Exception;
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

	protected $redis;

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->redis = Redis::connection('chat');
	}

	/**
	 * Execute the console command.
	 * @return mixed
	 * @throws Exception
	 */
	public function handle()
	{
		$rooms = $this->getRooms();
		if (!$rooms) return;

		foreach ($rooms as $room) {
			$this->moveToMysql($room);
		}

		return;
	}

	/**
	 * Copy rooms and move messages from redis to MySQL.
	 *
	 * @param string $room
	 * @throws Exception
	 */
	private function moveToMysql($room)
	{
		$room = ChatRoom::firstOrCreate(['name' => $room]);

		$rawMessages = $this->getMessages($room->name, $leave = 0);
		$messages = $this->decodeMessages($rawMessages);

		$this->transaction(function () use ($messages, $room, $rawMessages) {
			foreach ($messages as $key => $message) {
				$message = $this->formatMessage($message);
				$length = strlen($message['content']);
				if ($length > ChatMessage::MAX_MSG_CONTENT_LEN) {
					// Validation has to be done right by client and
					// live messaging server. If some invalid message
					// reaches this point, we're just skipping it.
					continue;
				}
				$room->messages()->create($message);
				$this->removeMessage($room->name, $rawMessages[$key]);
			}
		});
	}

	/**
	 * Search for chat rooms in redis DB.
	 *
	 * @param int $cursor
	 * @param int $limit
	 *
	 * @return mixed
	 */
	protected function scan($cursor, $limit = 1000)
	{
		return $this->redis->scan($cursor, 'MATCH', self::ROOM_MESSAGES_KEY . '*', 'COUNT', $limit);
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
		// as well as the end of the scan results list
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

	/**
	 * Get messages from chat room.
	 *
	 * @param string $room
	 * @param int $leave
	 * @param int $take
	 *
	 * @return mixed
	 */
	protected function getMessages($room, $leave = 200, $take = 100000)
	{
		return $this->redis->lrange(self::ROOM_MESSAGES_KEY . $room, -1 * ($take + $leave), -1 * ($leave + 1));
	}

	/**
	 * Remove message from chat storage.
	 *
	 * @param string $room
	 * @param array $message
	 *
	 * @return mixed
	 */
	protected function removeMessage($room, $message)
	{
		return $this->redis->lrem(self::ROOM_MESSAGES_KEY . $room, 0, $message);
	}

	/**
	 * Sanitize single message record
	 * before putting it into MySQL.
	 *
	 * @param array $message
	 *
	 * @return array
	 */
	protected function formatMessage($message)
	{
		if (empty ($message['user_id'])) {
			$message['user_id'] = null;
		}

		return [
			'content' => $message['content'],
			'user_id' => $message['user_id'],
			'time'    => $message['time'],
		];
	}

	/**
	 * Transform json encoded redis entries into array.
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	public function decodeMessages($messages)
	{
		// Much faster than running json_decode()
		// on each message separately.
		$messagesBundle = '[';
		foreach ($messages as $message) {
			$messagesBundle .= $message . ',';
		}
		$messageBundle = substr($messagesBundle, 0, -1);
		$messageBundle .= ']';

		$parsedMessages = json_decode($messageBundle, true);

		return (array)$parsedMessages;
	}

	/**
	 * Execute closure using double transaction (redis + MySQL).
	 *
	 * @param Closure $callback
	 *
	 * @throws Exception
	 */
	public function transaction(Closure $callback)
	{
		DB::beginTransaction();
		$this->redis->multi();

		try {
			$callback();
		}
		catch (Exception $e) {
			DB::rollBack();
			$this->redis->discard();
			throw $e;
		}

		DB::commit();
		$this->redis->exec();
	}
}
