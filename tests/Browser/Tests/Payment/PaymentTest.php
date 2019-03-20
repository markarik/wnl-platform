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
	public function tearDown()
	{
		// We set various properties on browser instance, so let's prevent leaking them between tests
		$this->closeAll();
	}

	public function testRegisterAndPayOnlineNow()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new AccountModule())->signUp($browser);
			(new PersonalDataModule())->submitNoInvoice($browser);
			(new ConfirmOrderModule())->payOnlineNow($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '1500.00');
			(new MyOrdersModule())->assertOrderPlaced($browser);
			(new MyOrdersModule())->assertPaid($browser, '1500zł / 1500zł');
		});
	}

	public function testRegisterAndPayOnlineLater()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new AccountModule())->signUp($browser);
			(new PersonalDataModule())->submitNoInvoice($browser);
			(new ConfirmOrderModule())->payOnlineLater($browser);
			(new MyOrdersModule())->assertOrderPlaced($browser);
			(new MyOrdersModule())->assertNotPaid($browser);
		});
	}

	public function testLoginAndPayOnlineLater()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new AccountModule())->logInUsingModal($browser);
			(new PersonalDataModule())->submitNoInvoice($browser);
			(new ConfirmOrderModule())->payOnlineLater($browser);
			(new MyOrdersModule())->assertOrderPlaced($browser);
			(new MyOrdersModule())->assertNotPaid($browser);
		});
	}

	public function testLogInEditDataAndOrder()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new UserModule())->existingUser($browser);
			(new VoucherModule())->skip($browser);
			(new PersonalDataModule())->submitNoInvoice($browser);
			(new ConfirmOrderModule())->editData($browser);
			(new PersonalDataModule())->submitCustomInvoice($browser);
			(new ConfirmOrderModule())->payOnlineNow($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '1500.00');
			(new MyOrdersModule())->assertOrderPlaced($browser);
			(new MyOrdersModule())->assertPaid($browser, '1500zł / 1500zł');
		});
	}

	public function testRegisterAndPayByInstalmentsLater()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new AccountModule())->signUp($browser);
			(new PersonalDataModule())->submitNoInvoice($browser);
			(new ConfirmOrderModule())->assertInstalments($browser);
			(new ConfirmOrderModule())->payByInstalmentsLater($browser);
			(new MyOrdersModule())->assertOrderPlaced($browser);
			(new MyOrdersModule())->assertInstalment($browser, 1, '0zł / 750zł');
			(new MyOrdersModule())->assertInstalment($browser, 2, '0zł / 375zł');
			(new MyOrdersModule())->assertInstalment($browser, 3, '0zł / 375zł');
			(new MyOrdersModule())->payNextInstalment($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '750.00');
			(new MyOrdersModule())->assertInstalment($browser, 1, '750zł / 750zł');
			(new MyOrdersModule())->assertInstalment($browser, 2, '0zł / 375zł');
			(new MyOrdersModule())->assertInstalment($browser, 3, '0zł / 375zł');
			(new MyOrdersModule())->payNextInstalment($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '375.00');
			(new MyOrdersModule())->assertInstalment($browser, 1, '750zł / 750zł');
			(new MyOrdersModule())->assertInstalment($browser, 2, '375zł / 375zł');
			(new MyOrdersModule())->assertInstalment($browser, 3, '0zł / 375zł');
			(new MyOrdersModule())->payNextInstalment($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '375.00');
			(new MyOrdersModule())->assertPaid($browser, '1500zł / 1500zł');
		});
	}

	public function testRegisterAndPayByInstalmentsNow()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new AccountModule())->signUp($browser);
			(new PersonalDataModule())->submitNoInvoice($browser);
			(new ConfirmOrderModule())->assertInstalments($browser);
			(new ConfirmOrderModule())->payByInstalmentsNow($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '750.00');
			(new MyOrdersModule())->assertOrderPlaced($browser);
			(new MyOrdersModule())->assertPaid($browser, '750zł');
			(new MyOrdersModule())->assertInstalment($browser, 1, '750zł / 750zł');
			(new MyOrdersModule())->assertInstalment($browser, 2, '0zł / 375zł');
			(new MyOrdersModule())->assertInstalment($browser, 3, '0zł / 375zł');
			(new MyOrdersModule())->payNextInstalment($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '375.00');
			(new MyOrdersModule())->assertInstalment($browser, 1, '750zł / 750zł');
			(new MyOrdersModule())->assertInstalment($browser, 2, '375zł / 375zł');
			(new MyOrdersModule())->assertInstalment($browser, 3, '0zł / 375zł');
			(new MyOrdersModule())->payNextInstalment($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '375.00');
			(new MyOrdersModule())->assertPaid($browser, '1500zł / 1500zł');
		});
	}

	public function testUseCouponAndPayByInstalmentsNow()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new VoucherModule())->code10Percent($browser);
			(new AccountModule())->signUp($browser);
			(new PersonalDataModule())->submitNoInvoice($browser);
			(new ConfirmOrderModule())->assertInstalments($browser, '675zł', '337.5zł', '337.5zł', '1350zł');
			(new ConfirmOrderModule())->payByInstalmentsNow($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '675.00');
			(new MyOrdersModule())->assertOrderPlaced($browser);
			(new MyOrdersModule())->assertPaid($browser, '675zł');
			(new MyOrdersModule())->assertInstalment($browser, 1, '675zł / 675zł');
			(new MyOrdersModule())->assertInstalment($browser, 2, '0zł / 337.5zł');
			(new MyOrdersModule())->assertInstalment($browser, 3, '0zł / 337.5zł');
			(new MyOrdersModule())->payNextInstalment($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '337.50');
			(new MyOrdersModule())->assertInstalment($browser, 1, '675zł / 675zł');
			(new MyOrdersModule())->assertInstalment($browser, 2, '337.5zł / 337.5zł');
			(new MyOrdersModule())->assertInstalment($browser, 3, '0zł / 337.5zł');
			(new MyOrdersModule())->payNextInstalment($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '337.50');
			(new MyOrdersModule())->assertPaid($browser, '1350zł / 1350zł');
		});
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
			(new ConfirmOrderModule())->assertInstalments($browser1);
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
		$this->browse(function (BethinkBrowser $browser) {
			(new VoucherModule())->code100Percent($browser);
			(new AccountModule())->signUp($browser);
			(new PersonalDataModule())->submitNoInvoice($browser);
			(new ConfirmOrderModule())->payByCoupon100Percent($browser);
			(new MyOrdersModule())->assertOrderPlaced($browser);
		});
	}

	public function testRegisterAndDontOrderPlatformForbidden()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new AccountModule())->signUp($browser);
			(new AccountModule())->assertNoAccessToPlatform($browser);
		});
	}

	public function testOrderWithPaidOrder()
	{
		$this->browse(function (BethinkBrowser $browser) {
			(new UserModule())->existingUserWithOrder($browser);
			(new PersonalDataModule())->submitNoInvoiceExistingOrder($browser);
			(new ConfirmOrderModule())->payOnlineNow($browser);
			(new OnlinePaymentModule())->successfulPayment($browser, '1500.00');
			(new MyOrdersModule())->assertOrderPlaced($browser);
			(new MyOrdersModule())->assertPaid($browser, '1500zł / 1500zł');
		});
	}
}
