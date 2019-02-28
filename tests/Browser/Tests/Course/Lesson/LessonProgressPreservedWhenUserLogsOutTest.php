<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Generator;
use Faker\Provider\Internet;
use Faker\Provider\Person;
use Tests\Browser\Pages\Course\Course;
use Tests\Browser\Pages\Course\Lesson;
use Tests\Browser\Pages\Course\Components\Navigation;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LessonProgressPreservedWhenUserLogsOutTest extends DuskTestCase
{

	private $user;
	private $password;
	private $email;

	public function setUp(): void
	{
		parent::setUp();

		$faker = new Generator();
		$faker->addProvider(new Person($faker));
		$faker->addProvider(new Internet($faker));

		$this->password = $faker->password;
		$this->email = $faker->unique()->safeEmail;

		$this->user = factory(User::class)->create(
			[
				'password' => bcrypt($this->password),
				'email' => $this->email
			]
		);
	}

	public function testLessonProgressPreservedWhenUserLogsOut()
	{
		$this->browse(function (Browser $browser) {
			$LESSON_COMPLETED = 2;
			$SECTION_COMPLETED = 2;

			$browser->loginAs($this->user)
				->visit(new Lesson($LESSON_COMPLETED))
				->waitFor('@side_nav', 15)
				->goToSection($SECTION_COMPLETED)
				->assertExpectedSectionActive($SECTION_COMPLETED)
				->on(new Navigation())
				->logoutUser()
				->on(new Login())
				->waitFor('@email_input')
				->loginAsUser($this->email, $this->password)
				->on(new Course())
				->waitFor('@side_nav', 15)
				->goToLesson($LESSON_COMPLETED)
				->on(new Lesson())
				->waitFor('@side_nav', 15)
				->assertExpectedSectionActive($SECTION_COMPLETED);
		});
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->user->delete();
	}
}
