<?php namespace App\Events;

use Request;
use Ramsey\Uuid\Uuid;

abstract class Event
{
	public $id;

	public $referer;

	public function __construct()
	{
		$this->referer = Request::header('X-BETHINK-LOCATION');
		$this->id = Uuid::uuid4()->toString();
	}
}
