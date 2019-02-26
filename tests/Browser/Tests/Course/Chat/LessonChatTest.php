<?php

namespace Tests\Browser;

use App\Models\User;
use Faker\Generator;
use Faker\Provider\Person;
use Tests\Browser\Pages\Course\Components\Chat;
use Tests\Browser\Pages\Course\Lesson;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LessonChatTest extends DuskTestCase
{
	private $firstName;
	private $lastName;
	private $firstName2;
	private $lastName2;
	private $user;
	private $user2;

	public function setUp(): void
	{
		parent::setUp();
		$faker = new Generator();
		$faker->addProvider(new Person($faker));

		$this->firstName = $faker->firstName(Person::GENDER_MALE);
		$this->lastName = $faker->lastName;

		$this->firstName2 = $faker->firstName(Person::GENDER_FEMALE);
		$this->lastName2 = $faker->lastName;

		$this->user = factory(User::class)->create(['first_name' => $this->firstName, 'last_name' => $this->lastName]);
		$this->user2 = factory(User::class)->create(['first_name' => $this->firstName2, 'last_name' => $this->lastName2]);
	}

	public function testLessonChat()
	{
		$this->browse(function (Browser $first, Browser $second) {
			$message1 = 'How are you? ' . time();
			$message2 = 'I\'m fine! ' . time();

			$userFullName1 = sprintf('%s %s', $this->firstName, $this->lastName);
			$userFullName2 = sprintf('%s %s', $this->firstName2, $this->lastName2);

			$first->loginAs($this->user)
				->visit(new Lesson())
				->waitUntilMissing('@loading_overlay', 30);

			$second->loginAs($this->user2)
				->visit(new Lesson())
				->waitUntilMissing('@loading_overlay', 30);


			$first->on(new Chat())
				->sendChatMessage($message1);

			$second->on(new Chat())
				->sendChatMessage($message2)
				->assertMessageFromUser($userFullName1, $message1);

			$first->assertMessageFromUser($userFullName2, $message2);
		});
	}

	public function tearDown()
	{
		parent::tearDown();
		$this->user->delete();
		$this->user2->delete();
	}
}
