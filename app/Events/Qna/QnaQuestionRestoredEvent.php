<?php

namespace App\Events\Qna;

use App\Events\ResourceRestoredEvent;

class QnaQuestionRestoredEvent extends ResourceRestoredEvent
{
	public $subject = 'qna_question';
	public $eventName = 'qna-question-resolved';
}
