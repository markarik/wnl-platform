<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
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

	public function submitNoAddress(BethinkBrowser $browser)
	{
		$this->navigate($browser);

		$browser->on(new PersonalDataPage());
		$this->submit($browser, false, false);
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
	}

	protected function submit(BethinkBrowser $browser, $invoice = false, $address = true)
	{
		$userData = $this->generatePersonalFormData();
		$this->fillInPersonalDataForm($userData, $browser, $invoice, $address);

		$browser->click('@submit-personal-data');

		$browser->user = User::where('email', $browser->accountData['email'])->first();
		$browser->order = $browser->user->orders()->recent();
		Assert::assertTrue($browser->user instanceof User);
		Assert::assertTrue($browser->order instanceof Order);
	}

	public function assertCartContainsCourse(BethinkBrowser $browser)
	{
		$this->navigate($browser);
		$browser->assertVisible('@cart');
		if (!empty($browser->coupon) && $browser->coupon->kind === Coupon::KIND_PARTICIPANT) {
			$browser->assertSeeIn('@cart', 'Album');
			$browser->assertSeeIn('@cart', 'Zakup kursu ze zniżką 50% nie obejmuje nowego albumu map myśli. Nowy album możesz zamówić osobno po opłaceniu zamówienia za kurs.');
		} else {
			$browser->assertSeeIn('@cart', 'Wysyłka');
			$browser->assertSeeIn('@cart', 'Na terenie Polski za darmo');
		}
		$browser->assertSeeIn('@cart', 'Dostęp od momentu wpłaty do');
		$browser->assertSeeIn('@cart', 'Kwota całkowita');
	}

	public function assertCartContainsAlbum(BethinkBrowser $browser)
	{
		$this->navigate($browser);
		$browser->assertVisible('@cart');
		$browser->assertSeeIn('@cart', Product::where(['slug' => Product::SLUG_WNL_ALBUM])->first()->name);
		$browser->assertSeeIn('@cart', 'Wysyłka');
		$browser->assertSeeIn('@cart', 'Na terenie Polski za darmo');
		$browser->assertSeeIn('@cart', 'Kwota całkowita');
	}

	protected function assertPersonalDataDisabled(BethinkBrowser $browser)
	{
		$browser->assertDisabled('first_name');
		$browser->assertDisabled('last_name');
		$browser->assertDisabled('personal_identity_number');
	}
}
