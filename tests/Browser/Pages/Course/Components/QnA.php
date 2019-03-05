<?php

namespace Tests\Browser\Pages\Course\Components;

use Laravel\Dusk\Page as BasePage;
use Tests\BethinkBrowser;

class QnA extends BasePage
{

	const CSS_QUESTION_SUBMIT = '.qna-new-question-form .button';

	public function url()
	{
		// no URL here...
	}

	public function elements()
	{
		return [
			'@post_question_button' => '.wnl-qna .level-right .is-small.is-primary.button.is-outlined',
			'@question_form' => '.qna-new-question-form .ql-editor',
			'@question_submit' => self::CSS_QUESTION_SUBMIT,
			'@question_container' => '.qna-question',
			'@post_answer_button' => '.qna-answers .button',
			'@answer_form' => '.qna-new-answer-form .ql-editor',
			'@answer_submit' => '.qna-new-answer-form .button',
			'@answer_container' => '.qna-answers',
			'@post_comment_button' => '.qna-answer-comments .secondary-link',
			'@comment_form' => '.qna-new-comment-form .ql-editor',
			'@comment_submit' => '.qna-new-comment-form .button'
		];
	}

	public function postQuestion(BethinkBrowser $browser, $question)
	{
		$browser
			->waitFor('@post_question_button')
			->click('@post_question_button')
			->type('@question_form', $question)
			->scrollTo(self::CSS_QUESTION_SUBMIT)
			->click('@question_submit');
	}

	public function assertQuestionPosted(BethinkBrowser $browser, $question)
	{
		$browser->with('@question_container', function ($questionContainer) use ($question) {
			$questionContainer->waitForText($question);
		});
	}

	public function answerQuestion(BethinkBrowser $browser, $answer)
	{
		$browser->with('@question_container', function ($questionContainer) use ($answer) {
			$questionContainer
				->click('@post_answer_button')
				->type('@answer_form', $answer)
				->click('@answer_submit');
		});
	}

	public function assertAnswerPosted(BethinkBrowser $browser, $answer)
	{
		$browser->with('@answer_container', function ($answerContainer) use ($answer) {
			$answerContainer->waitForText($answer);
		});
	}

	public function commentAnswer(BethinkBrowser $browser, $comment)
	{
		$browser->with('@answer_container', function ($answerContainer) use ($comment) {
			$answerContainer
				->click('@post_comment_button')
				->type('@comment_form', $comment)
				->click('@comment_submit');
		});
	}

	public function assertCommentPosted(BethinkBrowser $browser, $comment)
	{
		$browser->with('@answer_container', function ($answerContainer) use ($comment) {
			$answerContainer->waitForText($comment);
		});
	}
}
