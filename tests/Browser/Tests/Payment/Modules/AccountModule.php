<?php


namespace Tests\Browser\Tests\Payment\Modules;


use Faker\Generator;
use Faker\Provider\pl_PL\Person;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\LoginModal;
use Tests\Browser\Pages\Payment\AccountPage;
use Tests\Browser\Tests\Payment\SignsUpUsers;

class AccountModule
{
	use SignsUpUsers;

	public function signUp(BethinkBrowser $browser)
	{
		$this->navigate($browser);

		$browser->on(new AccountPage());

		$accountData = $this->generateAccountFormData();
		$this->fillInAccountForm($accountData, $browser);
		$browser->accountData = $accountData;

		$browser->click('@continue-button');
	}

	public function logInUsingModal(BethinkBrowser $browser)
	{
		if (!empty($browser->user)) return __CLASS__;

		$faker = new Generator();
		$faker->addProvider(new Person($faker));
		$user = factory(\App\Models\User::class)->create();
		$browser->accountData = $user->toArray();

		$this->navigate($browser);
		$browser->on(new AccountPage());
		$browser
			->click('@login')
			->on(new LoginModal)
			->loginAsUser($user->email, 'secret');
	}

	public function assertNoAccessToPlatform(BethinkBrowser $browser) {
		$browser
			->visit('/')
			->assertPathIs(
				(new AccountPage)->url()
			);
	}

	protected function navigate(BethinkBrowser $browser)
	{
		if (!str_is('*account*', $browser->getCurrentPath())) {
			$browser->visit('payment/account');
		}
	}
}
