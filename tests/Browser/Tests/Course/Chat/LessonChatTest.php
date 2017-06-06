<?php

namespace Tests\Browser;

use Tests\Browser\DataProviders\User;
use Tests\Browser\Pages\Course\Components\Chat;
use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LessonChatTest extends DuskTestCase
{

	public function testLessonChat()
	{
		$this->browse(function (Browser $first, Browser $second) {
			$message1 = 'How are you? ' . time();
			$message2 = 'I\m fine! ' . time();

			$userFullName1 = sprintf('%s %s', USER::USER_NAME_1, USER::USER_LAST_NAME_1);
			$userFullName2 = sprintf('%s %s', USER::USER_NAME_2, USER::USER_LAST_NAME_2);

			$first->maximize()
				->visit(new Login())
				->loginAsUser(User::USER_EMAIL_1, USER::USER_PASSWORD_1)
				->visit(new Lesson());

			$second->maximize()
				->visit(new Login())
				->loginAsUser(User::USER_EMAIL_2, USER::USER_PASSWORD_2)
				->visit(new Lesson());


			$first->on(new Chat())
				->sendLessonMessage($message1);

			$second->on(new Chat())
				->sendLessonMessage($message2)
				->assertMessageFromUser($userFullName1, $message1);

			$first->assertMessageFromUser($userFullName2, $message2);
		});
	}

}
