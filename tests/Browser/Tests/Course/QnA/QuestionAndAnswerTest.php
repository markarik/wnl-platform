<?php

namespace Tests\Browser;

use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Course\Components\QnA;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class QuestionAndAnswerTest extends DuskTestCase
{

	/**
	 * @dataProvider Tests\Browser\DataProviders\User::userProvider
	 * @param String $email
	 * @param String $password
	 * @param String $name
	 */
	public function testQuestionAnswerAndCommentPosted($email, $password, $name)
	{
		$this->browse(function (Browser $browser) use ($email, $password, $name) {
			$question = 'How u Doin\'? ' . time();
			$answer = 'Bitchen ' . time();
			$comment = 'I\'m fine as well ' . time();

			$browser->maximize()
				->visit(new Login())
				->loginAsUser($email, $password)
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
