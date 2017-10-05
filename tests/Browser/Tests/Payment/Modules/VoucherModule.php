<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Coupon;
use Tests\Browser\Pages\Payment\SelectProductPage;
use Tests\Browser\Pages\Payment\VoucherPage;

class VoucherModule
{
	public function default($browser)
	{
		$this->useCode($browser);
	}

	public function code10Percent($browser)
	{
		return $this->useCode($browser, 10);
	}

	public function code20Percent($browser)
	{
		return $this->useCode($browser, 20);
	}

	public function code100Percent($browser)
	{
		return $this->useCode($browser, 100);
	}

//	public function existingUser50PercCoupon()
//	{
//
//	}

	public function skip($browser)
	{
		if (!empty($browser->studyBuddy)) {
			return $this->useCode($browser);
		}

		$browser
			->visit(new VoucherPage())
			->click('@skip')
			->assertPathIs(
				(new SelectProductPage)->url()
			);

		return [
			SelectProductModule::class,
		];
	}

	protected function useCode($browser, $value = 10)
	{
		if (!empty($browser->studyBuddy)) {
			return $this->studyBuddy($browser);
		}

		$coupon = factory(Coupon::class)->create([
			'value' => $value,
		]);

		$browser->coupon = $coupon;

		$browser
			->visit(new VoucherPage())
			->type('code', $coupon->code)
			->click('@use')
			->assertPathIs(
				(new SelectProductPage)->url()
			);

		return [
			SelectProductModule::class,
		];
	}

	protected function studyBuddy($browser)
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

		return [
			SelectProductModule::class,
		];
	}
}
