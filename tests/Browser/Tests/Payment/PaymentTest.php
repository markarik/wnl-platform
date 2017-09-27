<?php

namespace Tests\Browser\Tests\Payment;

use Tests\Browser\Tests\Payment\Modules\ConfirmOrderModule;
use Tests\Browser\Tests\Payment\Modules\PersonalDataModule;
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
	use SignsUpUsers;

	/**
	 * @var (Faker) Factory
	 */
	protected $faker;

	/**
	 * Setup faker
	 */
	public function setUp()
	{
		parent::setUp();
		$this->faker = Factory::create();
	}

	/** @test */
	public function user_can_sign_up_and_place_order()
	{
		$this->browse(function ($browser) {
			$browser
				->visit(new SelectProductPage)
				->clickLink('Wybieram kurs stacjonarny');

			$user = $this->generateFormData($this->faker);
			$browser->on(new PersonalDataPage);
			$this->fillInForm($user, $browser);
			$browser->xpath('.//button[@class="button is-primary"]')->click();

			$browser
				->on(new ConfirmOrderPage)
				->assertSeeAll([$user['email'], $user['firstName'], $user['lastName'], $user['address']])
				->xpath('.//button[1]')->click();

			$browser
				->on(new OrdersPage)
				->waitForAll(['Twoje zamówienia', 'Zamówienie złożono']);

		});

		$this->closeAll();
	}

	/** @test */
	public function user_can_place_order_and_successfully_pay_online()
	{
		//TODO fix Platnosci24 webhook so it works on dev environment
		if (env('APP_ENV') !== 'sandbox') {
			$this->markTestSkipped(PHP_EOL . 'Omitting test ' . __METHOD__ . ' (applicable only on sandbox env)' . PHP_EOL);
		}

		$this->browse(function ($browser) {
			$browser
				->visit(new SelectProductPage)
				->clickLink('Wybieram kurs internetowy');

			$user = $this->generateFormData($this->faker);
			$browser->on(new PersonalDataPage);
			$this->fillInForm($user, $browser);
			$browser->xpath('.//button[@class="button is-primary"]')->click();

			$browser
				->on(new ConfirmOrderPage)
				->assertSeeAll([$user['email'], $user['firstName'], $user['lastName'], $user['address']])
				->press('button.p24-submit')
				->waitForText('Wybierz formę płatności', 30);

			$browser
				->on(new P24ChooseBank)
				->press('@ing-logo');

			try {
				$browser
					->waitFor('@accept-tou')
					//TODO this should be handled by pressing button not by executing script
					->executeScript("$('#reagulation-accept-button').click()");
			}
			catch (\Exception $e) {
				print PHP_EOL . '@accept-tou element not found' . PHP_EOL;
			}

			$browser
				->waitFor('@login-button')
				->press('@login-button')
				->press('@confirm-payment');

			$browser
				->waitForAll(['Twoje zamówienia', 'Zamówienie złożono'])
				->waitForText('Zapłacono', 60);
		});

		$this->closeAll();
	}

	/** @test */
	public function user_cant_place_order_and_request_an_invoice()
	{
		$this->browse(function ($browser) {
			$browser
				->visit(new SelectProductPage)
				->clickLink('Wybieram kurs internetowy');

			$user = $this->generateFormData($this->faker);
			$browser->on(new PersonalDataPage);
			$this->fillInForm($user, $browser, $invoice = true);
			$browser->xpath('.//button[@class="button is-primary"]')->click();

			$browser
				->on(new ConfirmOrderPage)
				->assertSeeAll([
					$user['email'],
					$user['firstName'],
					$user['lastName'],
					$user['address'],
					$user['invoice_company'],
					$user['invoice_address'],
					$user['invoice_postcode'],
					$user['invoice_country'],
				])
				->xpath('.//button[1]')->click();

			$browser
				->on(new OrdersPage)
				->waitForAll(['Twoje zamówienia', 'Zamówienie złożono']);
		});

		$this->closeAll();
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
	public function fromScenario()
	{
		if (!file_exists('scenario.dusk')) {
			print 'File scenario.dusk not found!';
		}

		$this->browse(function ($browser) {
			$contents = file_get_contents('scenario.dusk');
			$namespace = 'Tests\Browser\Tests\Payment\Modules\\';
			foreach (explode("\n", $contents) as $item) {
				if (!$item) continue;
//				fwrite(STDOUT, $item . PHP_EOL);
				list($module, $method) = explode('->', $item);
				$module = $namespace . $module;
				(new $module)->$method($browser);
			}
		});
	}
}
