<?php

namespace Tests\Browser\Lib;

use Facebook\WebDriver\Exception\TimeOutException;

class Wait
{
	public static function waitForElementHasClass($driver, $element, $class)
	{
		try {
			$driver->wait(10, 200)->until(ExpectedConditions::attributePresentInElement($element, 'class', $class));
		} catch (TimeOutException $ex) {
			return false;
		}
		return true;
	}
}


class ExpectedConditions
{
	public static function attributePresentInElement($element, $attribute, $value)
	{
		return function () use ($element, $attribute, $value) {
			return strpos($element->getAttribute($attribute), $value) !== false;
		};
	}
}
