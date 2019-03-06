<?php

namespace Tests\Browser\Pages\Course\Components;

use Laravel\Dusk\Page as BasePage;
use Tests\BethinkBrowser;

class Chat extends BasePage
{

	public function url()
	{
		// no url here...
	}

	public function elements()
	{
		return [
			'@chat_message_input' => '.chat-message-form .ql-editor',
			'@chat_message_submit' => 'button[name="wnl-chat-form-submit"]',
			'@chat_container' => '.wnl-chat-content'
		];
	}

	public function sendChatMessage(BethinkBrowser $browser, $message)
	{
		$browser
			->type('@chat_message_input', $message)
			->click('@chat_message_submit');
	}

	public function assertMessageFromUser(BethinkBrowser $browser, $userFullName, $message)
	{
		$browser->with('@chat_container', function ($chatContainer) use ($userFullName, $message) {
			$chatContainer
				->waitForText($userFullName)
				->waitForText($message);
		});
	}
}
