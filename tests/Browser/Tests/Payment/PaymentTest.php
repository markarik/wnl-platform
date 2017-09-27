<?php

namespace Tests\Browser\Tests\Payment;

use Tests\Browser\Tests\Payment\Modules\ConfirmOrderModule;
use Tests\Browser\Tests\Payment\Modules\MyOrdersModule;
use Tests\Browser\Tests\Payment\Modules\OnlinePaymentModule;
use Tests\Browser\Tests\Payment\Modules\PersonalDataModule;
use Tests\Browser\Tests\Payment\Modules\SelectProductModule;
use Tests\Browser\Tests\Payment\Modules\UserModule;
use Faker\Factory;
use Tests\Browser\Pages\Payment\ConfirmOrderPage;
use Tests\Browser\Pages\Payment\P24ChooseBank;
use Tests\Browser\Pages\Payment\PersonalDataPage;
use Tests\Browser\Pages\Payment\SelectProductPage;
use Tests\Browser\Pages\User\OrdersPage;
use Tests\Browser\Tests\Payment\Modules\VoucherModule;
use Tests\DuskTestCase;

class PaymentTest extends DuskTestCase
{
	/** @test */
	public function registerAndPayOnline()
	{
		$this->execute([
			UserModule::class          => 'newUser',
			SelectProductModule::class => 'onsite',
			PersonalDataModule::class  => 'signUpNoInvoice',
			ConfirmOrderModule::class  => 'payOnline',
			OnlinePaymentModule::class => 'successfulPayment',
			MyOrdersModule::class      => 'end',
		]);
	}

	/** @test */
	public function logInEditDataAndOrder()
	{
		$this->execute([
			UserModule::class          => 'existingUser',
			VoucherModule::class       => 'skip',
			SelectProductModule::class => 'online',
			ConfirmOrderModule::class  => 'editData',
			PersonalDataModule::class  => 'signUpCustomInvoice',
			ConfirmOrderModule::class  => 'payByTransfer',
			MyOrdersModule::class      => 'end',
		]);
	}

	/** @test */
	public function registerAndPayByInstalments()
	{
		$this->execute([
			UserModule::class          => 'newUser',
			SelectProductModule::class => 'online',
			PersonalDataModule::class  => 'signUpNoInvoice',
			ConfirmOrderModule::class  => 'payByInstalments',
			MyOrdersModule::class      => 'end',
		]);
	}

	/** @test */
	public function randomCheckoutTest()
	{
		if (file_exists('scenario.dusk')) unlink('scenario.dusk');
		$this->browse(function ($browser) {
			$next = UserModule::class;
			while ($next) $next = $this->callRandom($next, $browser);
		});
	}

	protected function callRandom($next, $browser)
	{
		if (is_array($next)) {
			$next = array_random($next);
		}
		$methods = get_class_methods($next);
		if (empty($methods)) {
			return false;
		}
		$method = array_random($methods);

		$action = class_basename($next) . '->' . $method . PHP_EOL;
		file_put_contents('scenario.dusk', $action, FILE_APPEND);

		return (new $next)->$method($browser);
	}

	/** @test */
	public function fromScenarioFile()
	{
		if (!file_exists('scenario.dusk')) {
			print 'File scenario.dusk not found!';
		}

		$this->browse(function ($browser) {
			$contents = file_get_contents('scenario.dusk');
			$namespace = 'Tests\Browser\Tests\Payment\Modules\\';
			foreach (explode("\n", $contents) as $item) {
				if (!$item) continue;
				fwrite(STDOUT, $item . PHP_EOL);
				list($module, $method) = explode('->', $item);
				$module = $namespace . $module;
				(new $module)->$method($browser);
			}
		});
	}

	protected function execute($scenario)
	{
		$this->browse(function ($browser) use ($scenario) {
			foreach ($scenario as $module => $method) {
				(new $module)->$method($browser);
			}
		});
	}
}
