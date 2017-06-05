<?php

namespace Tests\Browser\Library;

class Wait {
	static function waitForAttributeInElement($browser, $element, $attribute, $value) {
		$browser->driver
			->wait(5, 200)
			->until(
				ExpectedConditions::elementContainsAttribute($element, $attribute, $value)
			);

	}
}