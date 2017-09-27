<?php


namespace Tests\Browser\Tests\Payment\Modules;


class MyOrdersModule
{
	public function end($browser)
	{
		// make assertions
		return false;
	}

	public function studyBuddy($browser)
	{
		// make assertions
		dd($browser->order);
		// mark order as paid
		// refresh
		// start new session
		// return study buddy method of voucher module
	}

	protected function makeAssertions()
	{

	}
}
