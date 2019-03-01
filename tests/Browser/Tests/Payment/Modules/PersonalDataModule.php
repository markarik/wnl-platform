<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Order;
use App\Models\User;
use Faker\Generator;
use Faker\Provider\pl_PL\Person;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\LoginModal;
use Tests\Browser\Pages\Payment\PersonalDataPage;
use Tests\Browser\Tests\Payment\SignsUpUsers;
use PHPUnit\Framework\Assert;

class PersonalDataModule
{
	use SignsUpUsers;

	public function signUpNoInvoice(BethinkBrowser $browser)
	{
		$this->navigate($browser);

		$browser->on(new PersonalDataPage());
		$this->signUp($browser, false);
	}

	public function signUpCustomInvoice(BethinkBrowser $browser)
	{
		$this->navigate($browser);

		$browser->on(new PersonalDataPage());
		$this->signUp($browser, true);
	}

	public function logInUsingModal(BethinkBrowser $browser)
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
	}

	protected function navigate(BethinkBrowser $browser)
	{
		// Check if personal-data is current page, if not,
		// navigate to it using random product.
		if (!str_is('*personal-data*', $browser->getCurrentPath())) {
			$browser->visit('payment/personal-data/' . array_random(['wnl-online', 'wnl-online-onsite']));
		}
	}

	protected function signUp(BethinkBrowser $browser, $invoiceFlag)
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

	protected function isEdit(BethinkBrowser $browser)
	{
		return str_is('*?edit=true*', $browser->driver->getCurrentUrl());
	}
}
