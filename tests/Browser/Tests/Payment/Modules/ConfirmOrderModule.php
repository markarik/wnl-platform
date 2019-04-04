<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Coupon;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\ConfirmOrderPage;

class ConfirmOrderModule
{
	const METHOD_ONLINE_NOW = 'onlineNow';
	const METHOD_ONLINE_LATER = 'onlineLater';
	const METHOD_INSTALMENTS_LATER = 'instalmentsLater';
	const METHOD_INSTALMENTS_NOW = 'instalmentsNow';

	public function editData(BethinkBrowser $browser)
	{
		$browser->on(new ConfirmOrderPage);
		$browser->click('@edit-personal-data');
	}

	public function payOnlineNow(BethinkBrowser $browser)
	{
		$this->assertCart($browser);
		$browser
			->on(new ConfirmOrderPage)
			->click('@payment-method-now')
			->click('@submit-confirm-order');
	}

	public function payOnlineLater(BethinkBrowser $browser)
	{
		$this->assertCart($browser);

		$browser
			->on(new ConfirmOrderPage)
			->click('@payment-method-later')
			->click('@submit-confirm-order');
	}

	public function payByInstalmentsNow(BethinkBrowser $browser)
	{
		$this->assertCart($browser);

		$browser
			->on(new ConfirmOrderPage)
			->click('@payment-method-now')
			->click('@select-instalments')
			->click('@submit-confirm-order');
	}

	public function payByInstalmentsLater(BethinkBrowser $browser)
	{
		$this->assertCart($browser);

		$browser
			->on(new ConfirmOrderPage)
			->click('@payment-method-later')
			->click('@select-instalments')
			->click('@submit-confirm-order');
	}

	public function payByCoupon100Percent(BethinkBrowser $browser)
	{
		$this->assertCart($browser);

		$browser
			->on(new ConfirmOrderPage)
			->press('@submit-confirm-order');
	}

	protected function assertCart(BethinkBrowser $browser) {
		$order = $browser->order;

		$browser
			->assertVisible('@cart')
			->assertSeeIn('@cart', $order->product->name)
			->assertSeeIn('@cart', 'Dostęp od momentu wpłaty do')
			->assertSeeIn('@cart', 'Kwota całkowita')
			->assertSeeIn('@cart', $order->total_with_coupon);

		if (!empty($browser->coupon) && $browser->coupon->kind === Coupon::KIND_PARTICIPANT) {
			$browser
				->assertSeeIn('@cart', 'Album')
				->assertSeeIn('@cart', 'Zakup kursu ze zniżką 50% nie obejmuje nowego albumu map myśli. Nowy album możesz zamówić osobno po opłaceniu zamówienia za kurs.');
		} else {
			$browser
				->assertSeeIn('@cart', 'Wysyłka')
				->assertSeeIn('@cart', 'Na terenie Polski za darmo');
		}

		if (!empty($browser->coupon)) {
			$browser->assertSeeIn('@cart', 'Zniżka');
		}
	}

	public function assertInstalments(
		BethinkBrowser $browser,
		string $firstInstalmentAmount = '750zł',
		string $secondInstalmentAmount = '375zł',
		string $thirdInstalmentAmount = '375zł',
		string $totalAmount = '1500zł'
	)
	{
		$browser
			->on(new ConfirmOrderPage)
			->click('@open-instalments-modal')
			->pause(100)
			->assertSeeIn('[data-instalment-number="1"]', $firstInstalmentAmount)
			->assertSeeIn('[data-instalment-number="2"]', $secondInstalmentAmount)
			->assertSeeIn('[data-instalment-number="3"]', $thirdInstalmentAmount)
			->assertSeeIn('@instalments-total-amount', $totalAmount)
			->click('@close-instalments-modal');
	}
}
