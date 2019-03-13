<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Order;
use App\Models\User;
use Faker\Generator;
use Faker\Provider\pl_PL\Person;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\LoginModal;
use Tests\Browser\Pages\Payment\AccountPage;
use Tests\Browser\Pages\Payment\PersonalDataPage;
use Tests\Browser\Tests\Payment\SignsUpUsers;
use PHPUnit\Framework\Assert;

class AccountModule
{
	use SignsUpUsers;

	public function logInUsingModal(BethinkBrowser $browser)
	{
		if (!empty($browser->user)) return __CLASS__;

		$faker = new Generator();
		$faker->addProvider(new Person($faker));
		$user = factory(\App\Models\User::class)->create();
		$browser->user = $user;

		$this->navigate($browser);
		$browser->on(new AccountPage());
		$browser
			->click('@login')
			->on(new LoginModal)
			->loginAsUser($user->email, 'secret');
	}

	protected function navigate(BethinkBrowser $browser)
	{
		// Check if personal-data is current page, if not,
		// navigate to it using a product.
		if (!str_is('*account*', $browser->getCurrentPath())) {
			$browser->visit('payment/account');
		}
	}

	public function signUp(BethinkBrowser $browser)
	{
		$this->navigate($browser);

		$browser->on(new AccountPage());

		$accountData = $this->generateAccountFormData();
		$this->fillInAccountForm($accountData, $browser);
		$browser->accountData = $accountData;

		$browser->xpathClick('.//button[@class="button is-primary"]');
	}
}
