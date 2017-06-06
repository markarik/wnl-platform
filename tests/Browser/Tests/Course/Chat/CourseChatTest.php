<?php

namespace Tests\Browser;

use Tests\Browser\DataProviders\User;
use Tests\Browser\Pages\Course\Components\Chat;
use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class CourseChatTest extends DuskTestCase
{

	public function testCourseChat()
	{
		$this->browse(function (Browser $first, Browser $second) {
			$message1 = 'How are you? ' . time();
			$message2 = 'I\m fine! ' . time();

			$userFullName1 = sprintf('%s %s', USER::USER_NAME_1, USER::USER_LAST_NAME_1);
			$userFullName2 = sprintf('%s %s', USER::USER_NAME_2, USER::USER_LAST_NAME_2);

			$first->maximize()
				->visit(new Login())
				->loginAsUser(User::USER_EMAIL_1, USER::USER_PASSWORD_1)
				->visit(new Course());

			$second->maximize()
				->visit(new Login())
				->loginAsUser(User::USER_EMAIL_2, USER::USER_PASSWORD_2)
				->visit(new Course());


			$first->on(new Chat())
				->sendCourseMessage($message1);

			$second->on(new Chat())
				->sendCourseMessage($message2)
				->assertMessageFromUser($userFullName1, $message1);

			$first->assertMessageFromUser($userFullName2, $message2);
		});
	}
}
