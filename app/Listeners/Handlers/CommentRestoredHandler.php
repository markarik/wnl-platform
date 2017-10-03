<?php namespace App\Listeners\Handlers;

class CommentRestoredHandler extends Common\ResourceRestoredHandler
{

	protected function shouldNotify($event) {
		$commentAuthor = $event->model->user;
		$commentRemover = $event->data['actors']['id'] ?? 0;

		return $commentAuthor->id !== $commentRemover;
	}

	protected function getUserToNotify($event) {
		return $event->model->user;
	}
}
