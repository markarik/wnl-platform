<?php

namespace App\Events\Common;

use App\Traits\EventContextTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use App\Events\SanitizesUserContent;
use App\Events\Event;

abstract class ResourceRestored extends Event
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
