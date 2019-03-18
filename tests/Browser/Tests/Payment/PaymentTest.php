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

	public function testRegisterAndPayOnlineNow()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1500.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
		]);
	}

	public function testRegisterAndPayOnlineLater()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineLater'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertNotPaid'],
		]);
	}

	public function testLoginAndPayOnlineLater()
	{
		$this->execute([
			[AccountModule::class       , 'logInUsingModal'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineLater'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertNotPaid'],
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
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1500.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
		]);
	}

	public function testRegisterAndPayByInstalmentsLater()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalmentsLater'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertInstalment', [1, '0zł / 750zł']],
			[MyOrdersModule::class      , 'assertInstalment', [2, '0zł / 375zł']],
			[MyOrdersModule::class      , 'assertInstalment', [3, '0zł / 375zł']],
			[MyOrdersModule::class      , 'payNextInstalment'],
			[OnlinePaymentModule::class , 'successfulPayment', ['750.00']],
			[MyOrdersModule::class      , 'assertInstalment', [1, '750zł / 750zł']],
			[MyOrdersModule::class      , 'assertInstalment', [2, '0zł / 375zł']],
			[MyOrdersModule::class      , 'assertInstalment', [3, '0zł / 375zł']],
			[MyOrdersModule::class      , 'payNextInstalment'],
			[OnlinePaymentModule::class , 'successfulPayment', ['375.00']],
			[MyOrdersModule::class      , 'assertInstalment', [1, '750zł / 750zł']],
			[MyOrdersModule::class      , 'assertInstalment', [2, '375zł / 375zł']],
			[MyOrdersModule::class      , 'assertInstalment', [3, '0zł / 375zł']],
			[MyOrdersModule::class      , 'payNextInstalment'],
			[OnlinePaymentModule::class , 'successfulPayment', ['375.00']],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
		]);
	}

	/** @test */
	public function registerAndPayByInstalmentsNow()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalmentsNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['750.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['750zł']],
			[MyOrdersModule::class      , 'assertInstalment', [1, '750zł / 750zł']],
			[MyOrdersModule::class      , 'assertInstalment', [2, '0zł / 375zł']],
			[MyOrdersModule::class      , 'assertInstalment', [3, '0zł / 375zł']],
			[MyOrdersModule::class      , 'payNextInstalment'],
			[OnlinePaymentModule::class , 'successfulPayment', ['375.00']],
			[MyOrdersModule::class      , 'assertInstalment', [1, '750zł / 750zł']],
			[MyOrdersModule::class      , 'assertInstalment', [2, '375zł / 375zł']],
			[MyOrdersModule::class      , 'assertInstalment', [3, '0zł / 375zł']],
			[MyOrdersModule::class      , 'payNextInstalment'],
			[OnlinePaymentModule::class , 'successfulPayment', ['375.00']],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
		]);
	}

	public function testUseCouponAndPayByInstalmentsNow()
	{
		$this->execute([
			[VoucherModule::class       , 'code10Percent'],
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalmentsNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['675.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['675zł']],
			[MyOrdersModule::class      , 'assertInstalment', [1, '675zł / 675zł']],
			[MyOrdersModule::class      , 'assertInstalment', [2, '0zł / 337.5zł']],
			[MyOrdersModule::class      , 'assertInstalment', [3, '0zł / 337.5zł']],
			[MyOrdersModule::class      , 'payNextInstalment'],
			[OnlinePaymentModule::class , 'successfulPayment', ['337.50']],
			[MyOrdersModule::class      , 'assertInstalment', [1, '675zł / 675zł']],
			[MyOrdersModule::class      , 'assertInstalment', [2, '337.5zł / 337.5zł']],
			[MyOrdersModule::class      , 'assertInstalment', [3, '0zł / 337.5zł']],
			[MyOrdersModule::class      , 'payNextInstalment'],
			[OnlinePaymentModule::class , 'successfulPayment', ['337.50']],
			[MyOrdersModule::class      , 'assertPaid', ['1350zł / 1350zł']],
		]);
	}

	public function testStudyBuddyOriginalOrderPaidOnline()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1500.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
			[MyOrdersModule::class      , 'studyBuddyInitiator'],
			[VoucherModule::class       , 'codeStudyBuddy'],
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1400.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertStudyBuddyAwaitingRefund'],
		]);
	}

	public function testStudyBuddyOriginalOrderPaidByInstalments()
	{
		$this->execute([
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalmentsNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['750.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'studyBuddyInitiator'],
			[VoucherModule::class       , 'codeStudyBuddy'],
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1400.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertStudyBuddyRefunded'],
		]);
	}

	public function testFreeCourseCoupon()
	{
		$this->execute([
			[VoucherModule::class       , 'code100Percent'],
			[AccountModule::class       , 'signUp'],
			[PersonalDataModule::class  , 'submitNoInvoice'],
			[ConfirmOrderModule::class  , 'payByCoupon100Percent'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
		]);
	}

	public function testRegisterAndDontOrderPlatformForbidden()
	{
		$this->execute([
			[AccountModule::class, 'signUp'],
			[AccountModule::class, 'assertNoAccessToPlatform'],
		]);
	}

	public function testOrderWithPaidOrder()
	{
		$this->execute([
			[UserModule::class          , 'existingUserWithOrder'],
			[PersonalDataModule::class  , 'submitNoInvoiceExistingOrder'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1500.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
		]);
	}
}
