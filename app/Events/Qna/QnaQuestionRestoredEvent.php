<?php

namespace App\Events\Qna;

use App\Events\Live\ResourceRestoredEvent;

class QnaQuestionRestoredEvent extends ResourceRestoredEvent
{
	public $subject = 'qna_question';
	public $eventName = 'qna-question-resolved';
}
