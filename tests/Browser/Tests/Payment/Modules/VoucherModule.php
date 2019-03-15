<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Coupon;
use Tests\BethinkBrowser;
use Tests\Browser\Pages\Payment\SelectProductPage;
use Tests\Browser\Pages\Payment\VoucherPage;

class VoucherModule
{
	public function code10Percent(BethinkBrowser $browser)
	{
		$this->useCode($browser, 10);
	}

	public function code100Percent($browser)
	{
		$this->useCode($browser, 100);
	}

	public function skip(BethinkBrowser $browser)
	{
		$browser
			->visit(new VoucherPage())
			->click('@skip')
			->assertPathIs(
				(new SelectProductPage)->url()
			);
	}

	protected function useCode(BethinkBrowser $browser, int $value)
	{
		$coupon = factory(Coupon::class)->create([
			'value' => $value
		]);

		$browser->coupon = $coupon;

		$browser
			->visit(new VoucherPage())
			->type('code', $coupon->code)
			->click('@use')
			->assertPathIs(
				(new SelectProductPage)->url()
			);
	}

	public function codeStudyBuddy(BethinkBrowser $browser)
	{
		$studyBuddy = $browser->studyBuddy;

		$browser->coupon = $studyBuddy->coupon;

		$browser
			->visit(new VoucherPage)
			->type('code', $browser->studyBuddy->code)
			->click('@use')
			->assertPathIs(
				(new SelectProductPage)->url()
			);
	}
}
