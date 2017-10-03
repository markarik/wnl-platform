<?php namespace App\Listeners\Handlers;

class QnaQuestionRestoredHandler extends Common\ResourceRestoredHandler
{
	protected function shouldNotify($event) {
		$qnaQuestionAuthor = $event->model->user;
		$qnaQuestionRemover = $event->data['actors']['id'] ?? 0;

		return $qnaQuestionAuthor->id !== $qnaQuestionRemover;
	}

	protected function getUserToNotify($event) {
		return $event->model->user;
	}
}
