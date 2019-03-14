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
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid'],
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
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid'],
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
			[MyOrdersModule::class      , 'assertInstalmentNotPaid'],
		]);
	}

	/** @test */
	public function registerAndPayByInstalmentsNow()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payByInstalmentsNow'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid'],
			[MyOrdersModule::class      , 'assertInstalmentPaid'],
		]);
	}

	/** @test */
	public function studyBuddyOriginalOrderPaidOnline()
	{
		$this->execute([
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid'],
			[MyOrdersModule::class      , 'studyBuddyInitiator'],
			[VoucherModule::class       , 'default'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment'],
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
			[OnlinePaymentModule::class , 'successfulPayment'],
			[MyOrdersModule::class      , 'assertOrderPlaced'],
			[MyOrdersModule::class      , 'assertPaid'],
			[MyOrdersModule::class      , 'assertInstalmentPaid'],
			[MyOrdersModule::class      , 'studyBuddyInitiator'],
			[VoucherModule::class       , 'default'],
			[SelectProductModule::class , 'online'],
			[PersonalDataModule::class  , 'signUpNoInvoice'],
			[ConfirmOrderModule::class  , 'payOnlineNow'],
			[OnlinePaymentModule::class , 'successfulPayment'],
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
