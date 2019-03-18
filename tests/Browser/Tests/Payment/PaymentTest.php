<?php

namespace Tests\Browser\Tests\Payment;

use Tests\BethinkBrowser;
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

	public function tearDown()
	{
		// We set various properties on browser instance, so let's prevent leaking them between tests
		$this->closeAll();
	}

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
		$this->browse(function (BethinkBrowser $browser1, BethinkBrowser $browser2) {
			(new AccountModule())->signUp($browser1);
			(new PersonalDataModule())->submitNoInvoice($browser1);
			(new ConfirmOrderModule())->payOnlineNow($browser1);
			(new OnlinePaymentModule())->successfulPayment($browser1, '1500.00');
			(new MyOrdersModule())->assertOrderPlaced($browser1);
			(new MyOrdersModule())->assertPaid($browser1, '1500zł / 1500zł');
			$studyBuddy = (new MyOrdersModule())->studyBuddyInitiator($browser1);

			(new VoucherModule())->codeStudyBuddy($browser2, $studyBuddy);
			(new AccountModule())->signUp($browser2);
			(new PersonalDataModule())->submitNoInvoice($browser2);
			(new ConfirmOrderModule())->payOnlineNow($browser2);
			(new OnlinePaymentModule())->successfulPayment($browser2, '1400.00');
			(new MyOrdersModule())->assertOrderPlaced($browser2);

			(new MyOrdersModule())->assertStudyBuddyAwaitingRefund($browser1);
		});
	}

	public function testStudyBuddyOriginalOrderPaidByInstalments()
	{
		$this->browse(function (BethinkBrowser $browser1, BethinkBrowser $browser2) {
			(new AccountModule())->signUp($browser1);
			(new PersonalDataModule())->submitNoInvoice($browser1);
			(new ConfirmOrderModule())->payByInstalmentsNow($browser1);
			(new OnlinePaymentModule())->successfulPayment($browser1, '750.00');
			(new MyOrdersModule())->assertOrderPlaced($browser1);
			(new MyOrdersModule())->assertPaid($browser1, '750zł');
			(new MyOrdersModule())->assertInstalment($browser1, 1, '750zł / 750zł');
			(new MyOrdersModule())->assertInstalment($browser1, 2, '0zł / 375zł');
			(new MyOrdersModule())->assertInstalment($browser1, 3, '0zł / 375zł');
			$studyBuddy = (new MyOrdersModule())->studyBuddyInitiator($browser1);

			(new VoucherModule())->codeStudyBuddy($browser2, $studyBuddy);
			(new AccountModule())->signUp($browser2);
			(new PersonalDataModule())->submitNoInvoice($browser2);
			(new ConfirmOrderModule())->payOnlineNow($browser2);
			(new OnlinePaymentModule())->successfulPayment($browser2, '1400.00');
			(new MyOrdersModule())->assertOrderPlaced($browser2);

			$browser1->refresh();
			(new MyOrdersModule())->assertInstalment($browser1, 1, '700zł / 700zł');
			(new MyOrdersModule())->assertInstalment($browser1, 2, '50zł / 350zł');
			(new MyOrdersModule())->assertInstalment($browser1, 3, '0zł / 350zł');
			(new MyOrdersModule())->payNextInstalment($browser1);
			(new OnlinePaymentModule())->successfulPayment($browser1, '300.00');
			(new MyOrdersModule())->assertPaid($browser1, '1050zł');
			(new MyOrdersModule())->assertInstalment($browser1, 1, '700zł / 700zł');
			(new MyOrdersModule())->assertInstalment($browser1, 2, '350zł / 350zł');
			(new MyOrdersModule())->assertInstalment($browser1, 3, '0zł / 350zł');
			(new MyOrdersModule())->payNextInstalment($browser1);
			(new OnlinePaymentModule())->successfulPayment($browser1, '350.00');
			(new MyOrdersModule())->assertPaid($browser1, '1400zł / 1400zł');
		});
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
