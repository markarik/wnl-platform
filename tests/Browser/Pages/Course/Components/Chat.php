<?php

namespace Tests\Browser\Pages\Course\Components;

use Laravel\Dusk\Page as BasePage;

class Chat extends BasePage
{

	public function url()
	{
		// no url here...
	}

	public function elements()
	{
		return [
			'@chat_message_course_input' => '#wnl-chat-form-courses-1',
			'@chat_message_lesson_input' => '#wnl-chat-form-courses-1-lessons-1',
			'@chat_message_submit' => 'button[name="wnl-chat-form-submit"]',
			'@chat_container' => '.wnl-chat-content'
		];
	}

	public function sendCourseMessage($browser, $message)
	{
		$browser
			->type('@chat_message_course_input', $message)
			->click('@chat_message_submit');
	}

	public function sendLessonMessage($browser, $message)
	{
		$browser
			->type('@chat_message_lesson_input', $message)
			->click('@chat_message_submit');
	}

	public function assertMessageFromUser($browser, $userFullName, $message)
	{
		$browser->with('@chat_container', function ($chatContainer) use ($userFullName, $message) {
			$chatContainer
				->waitForText($userFullName)
				->waitForText($message);
		});
	}
}
