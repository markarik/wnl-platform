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
	public function registerAndPayOnlineNow()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1500.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
		]);
	}

	/** @test */
	public function registerAndPayOnlineLater()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineLater'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertNotPaid'],
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
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1500.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
		]);
	}

	/** @test */
	public function registerAndPayByInstalmentsLater()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
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
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
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

	/** @test */
	public function useCouponAndPayByInstalmentsNow()
	{
		$this->execute([
			[VoucherModule::class       , 'code10Percent'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
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

	/** @test */
	public function studyBuddyOriginalOrderPaidOnline()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1500.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid', ['1500zł / 1500zł']],
			[MyOrdersModule::class      , 'studyBuddyInitiator'],
			[VoucherModule::class       , 'codeStudyBuddy'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1400.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertStudyBuddyAwaitingRefund'],
		]);
	}

	/** @test */
	public function studyBuddyOriginalOrderPaidByInstalments()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalmentsNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['750.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'studyBuddyInitiator'],
			[VoucherModule::class       , 'codeStudyBuddy'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment', ['1400.00']],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertStudyBuddyRefunded'],
		]);
	}

	/** @test */
	public function freeCourseCoupon()
	{
		$this->execute([
			[VoucherModule::class       , 'code100Percent'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payByCoupon100Percent'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
		]);
	}
}
