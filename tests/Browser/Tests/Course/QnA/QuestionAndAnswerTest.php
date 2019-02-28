<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Course\Components\QnA;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class QuestionAndAnswerTest extends DuskTestCase
{

	private $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = factory(User::class)->create();
	}

	public function testQuestionAnswerAndCommentPosted()
	{
		$this->browse(function (Browser $browser) {
			$question = 'How u Doin\'? ' . time();
			$answer = 'Bitchen ' . time();
			$comment = 'I\'m fine as well ' . time();

			$browser->loginAs($this->user)
				->visit(new Lesson())
				->waitFor('@side_nav', 15)
				->on(new QnA())
				->postQuestion($question)
				->waitFor('@post_question_button')
				->assertQuestionPosted($question)
				->answerQuestion($answer)
				->waitFor('@post_answer_button')
				->assertAnswerPosted($answer)
				->commentAnswer($comment)
				->waitUntilMissing('@comment_form')
				->assertCommentPosted($comment);
		});
	}
}
