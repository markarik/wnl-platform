<?php

namespace App\Events\Live;

use App\Events\Event;
use App\Events\SanitizesUserContent;
use App\Traits\EventContextTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class ResourceRestoredEvent extends Event
{
	use Dispatchable,
		InteractsWithSockets,
		SanitizesUserContent,
		SerializesModels,
		EventContextTrait;

	public $model;
	public $userId;
	public $eventName;
	public $subject;

	/**
	 *
	 * @param Model $model
	 * @param int $userId
	 */
	public function __construct(Model $model, $userId)
	{
		parent::__construct();
		$this->model = $model;
		$this->userId = $userId;
	}

	public function transform()
	{
		$this->data = [
			'event'   => $this->eventName,
			'subject' => [
				'type' => $this->subject
			],
			'actors'  => [
				'id' => $this->userId
			]
		];
	}
}
