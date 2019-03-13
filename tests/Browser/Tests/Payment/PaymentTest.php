<?php

namespace Tests\Browser\Tests\Payment;

use Tests\Browser\Tests\Payment\Modules\AccountModule;
use Tests\Browser\Tests\Payment\Modules\ConfirmOrderModule;
use Tests\Browser\Tests\Payment\Modules\MyOrdersModule;
use Tests\Browser\Tests\Payment\Modules\OnlinePaymentModule;
use Tests\Browser\Tests\Payment\Modules\PersonalDataModule;
use Tests\Browser\Tests\Payment\Modules\UserModule;
use Tests\Browser\Tests\Payment\Modules\VoucherModule;
use Tests\DuskTestCase;

class PaymentTest extends DuskTestCase
{
	use ExecutesScenarios;

	public function testRegisterAndPayOnline()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnline'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	public function testLoginAndPayOnline()
	{
		$this->execute([
			[AccountModule::class       , 'logInUsingModal'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnline'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	public function testLogInEditDataAndOrder()
	{
		$this->execute([
			[UserModule::class          , 'existingUser'],
			[VoucherModule::class       , 'skip'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'editData'],
			[PersonalDataModule::class  , 'submitCustomInvoice'],
			[ConfirmOrderModule::class  , 'payOnline'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	public function testRegisterAndPayByInstalments()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalments'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	public function testStudyBuddy()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalments'],
			[MyOrdersModule::class      , 'studyBuddy'],
			[VoucherModule::class       , 'default'],
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnline'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	public function testFreeCourseCoupon()
	{
		$this->execute([
			[VoucherModule::class       , 'code100Percent'],
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payByTransfer'],
			[MyOrdersModule::class      , 'end'],
		]);
	}

	public function testRegisterAndDontOrderPlatformForbidden()
	{
		$this->execute([
			[AccountModule::class, 'signUp'],
			[AccountModule::class, 'assertNoAccessToPlatform'],
		]);
	}
}
