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
			[UserModule::class          , 'newUser'],
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
			[ConfirmOrderModule::class  , 'editData'],
			[PersonalDataModule::class  , 'signUpCustomInvoice'],
			[ConfirmOrderModule::class  , 'payByTransfer'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	/** @test */
	public function registerAndPayByInstalments()
	{
		$this->execute([
			[UserModule::class          , 'newUser'],
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
			[UserModule::class          , 'newUser'],
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
			[UserModule::class          , 'newUser'],
			[VoucherModule::class       , 'code100Percent'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payByTransfer'],
			[MyOrdersModule::class      , 'end'],
		]);
	}
}
