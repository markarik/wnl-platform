<?php

namespace App\Events\Comments;

use App\Events\ResourceRestoredEvent;

class CommentRestored extends ResourceRestoredEvent
{
	public $eventName = 'comment-resolved';
	public $subject = 'comment';
}
