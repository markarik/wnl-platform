<?php

namespace Tests\Browser\Library;

use Facebook\WebDriver\Exception\StaleElementReferenceException;

class ExpectedConditions
{

	static function elementContainsAttribute($element, $attribute, $value)
	{

		return function () use ($element, $attribute, $value) {
			try {
				return strpos($element->getAttribute($attribute), $value) !== false;
			} catch (StaleElementReferenceException $ex) {
				//@TODO figure out how to avoid this exception
				return true;
			}
		};
	}
}