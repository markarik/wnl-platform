<?php

namespace App\Events;

class CommentRestored extends Common\ResourceRestored
{
	public $eventName = 'comment-resolved';
	public $subject = 'comment';
}
