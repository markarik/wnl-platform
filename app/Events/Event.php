<?php namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Ramsey\Uuid\Uuid;
use Request;

abstract class Event
{
	public $id;

	public $referer;

	public $data;

	const EVENT_RESOURCE_MAP = [
		'removed' => [
			'qna_questions' => 'App\Events\Qna\QnaQuestionRemoved',
			'qna_answers'   => 'App\Events\Qna\QnaAnswerRemoved',
			'comments'      => 'App\Events\Comments\CommentRemoved',
			'slides'        => 'App\Events\Slides\SlideRemoved',
		],
	];

	public function __construct()
	{
		$this->referer = Request::header('X-BETHINK-LOCATION');
		$this->id = Uuid::uuid4()->toString();

		if (!Request::header('X-Socket-ID')) {
			\Log::error('X-Socket-ID header missing');
		}
	}

	/**
	 * Get event class name basing on action and resource name.
	 *
	 * @param $resourceName
	 * @param $action
	 *
	 * @return string
	 */
	public static function getResourceEvent($resourceName, $action)
	{
		return self::EVENT_RESOURCE_MAP[$action][$resourceName] ?? null;
	}

	/**
	 * Get the channels the event should broadcast on.
	 * This method should be overridden inside each event
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('channel-name');
	}
}
