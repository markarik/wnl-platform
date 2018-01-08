<?php

namespace App\Events\Comments;

use App\Events\Live\ResourceRestoredEvent;

class CommentRestoredEvent extends ResourceRestoredEvent
{
	public $eventName = 'comment-resolved';
	public $subject = 'comment';
}
