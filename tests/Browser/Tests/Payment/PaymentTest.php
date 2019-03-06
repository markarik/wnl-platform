<?php

namespace Tests\Browser\Tests\Payment;

use Tests\Browser\Tests\Payment\Modules\ConfirmOrderModule;
use Tests\Browser\Tests\Payment\Modules\MyOrdersModule;
use Tests\Browser\Tests\Payment\Modules\OnlinePaymentModule;
use Tests\Browser\Tests\Payment\Modules\PersonalDataModule;
use Tests\Browser\Tests\Payment\Modules\SelectProductModule;
use Tests\Browser\Tests\Payment\Modules\UserModule;
use Tests\Browser\Tests\Payment\Modules\VoucherModule;
use Tests\DuskTestCase;

class PaymentTest extends DuskTestCase
{
	use ExecutesScenarios;

	/** @test */
	public function registerAndPayOnline()
	{
		$this->execute([
			[SelectProductModule::class , 'onsite'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnline'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	/** @test */
	public function logInEditDataAndOrder()
	{
		$this->execute([
			[UserModule::class          , 'existingUser'],
			[VoucherModule::class       , 'skip'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'editData'],
			[PersonalDataModule::class  , 'signUpCustomInvoice'],
			[ConfirmOrderModule::class  , 'payOnline'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	/** @test */
	public function registerAndPayByInstalments()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalments'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	/** @test */
	public function studyBuddy()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalments'],
			[MyOrdersModule::class      , 'studyBuddy'],
			[VoucherModule::class       , 'default'],
			[SelectProductModule::class , 'onsite'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnline'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	/** @test */
	public function freeCourseCoupon()
	{
		$this->execute([
			[VoucherModule::class       , 'code100Percent'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payByTransfer'],
			[MyOrdersModule::class      , 'end'],
		]);
	}
}
