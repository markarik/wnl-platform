<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Order;
use App\Models\User;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\PersonalDataPage;
use Tests\Browser\Tests\Payment\SignsUpUsers;
use PHPUnit\Framework\Assert;

class PersonalDataModule
{
	use SignsUpUsers;

	public function submitNoInvoice(BethinkBrowser $browser)
	{
		$this->navigate($browser);

		$browser->on(new PersonalDataPage());
		$this->submit($browser, false);
	}

	public function submitNoInvoiceExistingOrder(BethinkBrowser $browser)
	{
		$this->navigate($browser);

		$browser->on(new PersonalDataPage());
		$this->assertPersonalDataDisabled($browser);
		$this->submit($browser, false);
	}

	public function submitCustomInvoice(BethinkBrowser $browser)
	{
		$this->navigate($browser);

		$browser->on(new PersonalDataPage());
		$this->submit($browser, true);
	}

	protected function navigate(BethinkBrowser $browser)
	{
		// Check if personal-data is current page, if not,
		// navigate to it using a product.
		if (!str_is('*personal-data*', $browser->getCurrentPath())) {
			$browser->visit('payment/personal-data');
		}

		$browser->on(new PersonalDataPage());

		$this->assertCart($browser);
	}

	protected function submit(BethinkBrowser $browser, $invoiceFlag)
	{
		$userData = $this->generatePersonalFormData();
		$this->fillInPersonalDataForm($userData, $browser, $invoiceFlag);

		$browser->click('@submit-personal-data');

		$browser->user = User::where('email', $browser->accountData['email'])->first();
		$browser->order = $browser->user->orders()->recent();
		Assert::assertTrue($browser->user instanceof User);
		Assert::assertTrue($browser->order instanceof Order);
	}

	protected function assertCart(BethinkBrowser $browser)
	{
		$browser->assertVisible('@cart');
		$browser->assertSeeIn('@cart', 'Wysyłka');
		$browser->assertSeeIn('@cart', 'Na terenie Polski za darmo');
		$browser->assertSeeIn('@cart', 'Dostęp od momentu wpłaty do');
		$browser->assertSeeIn('@cart', 'Kwota całkowita');
	}

	protected function assertPersonalDataDisabled(BethinkBrowser $browser)
	{
		$browser->assertDisabled('first_name');
		$browser->assertDisabled('last_name');
		$browser->assertDisabled('personal_identity_number');
	}
}
