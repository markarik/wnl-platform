<?php

namespace App\Events;

use App\Events\Common\ResourceRestored;

class QnaQuestionRestored extends ResourceRestored
{
	public $subject = 'qna_question';
	public $eventName = 'qna-question-resolved';
}
