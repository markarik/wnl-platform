<?php


namespace Tests\Browser\Tests\Payment\Modules;


use App\Models\Coupon;
use Tests\Browser\Pages\Payment\SelectProductPage;
use Tests\Browser\Pages\Payment\VoucherPage;

class VoucherModule
{
	public function useCode($browser)
	{
		$coupon = factory(Coupon::class)->create();

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

	public function skip($browser)
	{
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
}
