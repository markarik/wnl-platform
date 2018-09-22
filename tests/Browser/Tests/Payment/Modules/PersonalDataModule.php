<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Order;
use App\Models\User;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\pl_PL\Person;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Tests\Browser\Pages\Login;
use Tests\Browser\Pages\LoginModal;
use Tests\Browser\Pages\Payment\PersonalDataPage;
use Tests\Browser\Tests\Payment\SignsUpUsers;
use PHPUnit\Framework\Assert;

class PersonalDataModule extends TestModule
{
	use SignsUpUsers;

	public function signUpNoInvoice($browser)
	{
		$this->navigate($browser);

		$browser->on(new PersonalDataPage());
		$this->signUp($browser, false);

		return ConfirmOrderModule::class;
	}

	public function signUpCustomInvoice($browser)
	{
		$this->navigate($browser);

		$browser->on(new PersonalDataPage());
		$this->signUp($browser, true);

		return ConfirmOrderModule::class;
	}

	public function logInUsingModal($browser)
	{
		if (!empty($browser->user)) return __CLASS__;

		$faker = new Generator();
		$faker->addProvider(new Person($faker));
		$user = factory(\App\Models\User::class)->create();
		$browser->user = $user;

		$this->navigate($browser);
		$browser->on(new PersonalDataPage());
		$browser
			->click('@login')
			->on(new LoginModal)
			->loginAsUser($user->email, 'secret');

		return ConfirmOrderModule::class;
	}

	protected function navigate($browser)
	{
		// Check if personal-data is current page, if not,
		// navigate to it using random product.
		if (!str_is('*personal-data*', $browser->getCurrentPath())) {
			$browser->visit('payment/personal-data/' . array_random(['wnl-online', 'wnl-online-onsite']));
		}
	}

	protected function signUp($browser, $invoiceFlag)
	{
		$userData = $this->generateFormData();
		$this->fillInForm($userData, $browser, $invoiceFlag, !$this->isEdit($browser));
		$browser->userData = $userData;

		$browser->xpathClick('.//button[@class="button is-primary"]');

		if (!$this->isEdit($browser)) {
			$browser->user = User::where('email', $userData['email'])->first();
			$browser->order = $browser->user->orders()->recent();
			Assert::assertTrue($browser->user instanceof User);
			Assert::assertTrue($browser->order instanceof Order);
		}
	}

	protected function isEdit($browser)
	{
		return str_is('*?edit=true*', $browser->driver->getCurrentUrl());
	}
}
