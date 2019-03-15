<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Coupon;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\AccountPage;
use Tests\Browser\Pages\Payment\VoucherPage;

class VoucherModule
{
	public function default(BethinkBrowser $browser)
	{
		$this->useCode($browser);
	}

	public function code100Percent($browser)
	{
		$this->useCode($browser, 100);
	}

	public function skip(BethinkBrowser $browser)
	{
		if (!empty($browser->studyBuddy)) {
			$this->useCode($browser);
			return;
		}

		$browser
			->visit(new VoucherPage())
			->click('@skip')
			->assertPathIs(
				(new AccountPage)->url()
			);
	}

	protected function useCode(BethinkBrowser $browser, $value = 10)
	{
		if (!empty($browser->studyBuddy)) {
			$this->studyBuddy($browser);
			return;
		}

		$coupon = factory(Coupon::class)->create([
			'value' => $value
		]);

		$browser->coupon = $coupon;

		$browser
			->visit(new VoucherPage())
			->type('code', $coupon->code)
			->click('@use')
			->assertPathIs(
				(new AccountPage)->url()
			);
	}

	protected function studyBuddy(BethinkBrowser $browser)
	{
		$studyBuddy = $browser->studyBuddy;

		$browser->coupon = $studyBuddy->coupon;

		$browser
			->visit(new VoucherPage)
			->type('code', $browser->studyBuddy->code)
			->click('@use')
			->assertPathIs(
				(new AccountPage)->url()
			);
	}
}
