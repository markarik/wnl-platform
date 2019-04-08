<?php

namespace Tests\Unit\Rules;

use App\Rules\ValidatePersonalIdentityNumber;
use Tests\TestCase;

class PersonalIdentityNumberTest extends TestCase
{
	public function testAllZeroCase() {
		$validator = new ValidatePersonalIdentityNumber();

		$this->assertFalse($validator->passes('', '00000000000'));
	}

	public function testValidDate() {
		$validator = new ValidatePersonalIdentityNumber();

		$this->assertTrue($validator->passes('', '63111392985'));
	}

	public function testFutureDate() {
		$validator = new ValidatePersonalIdentityNumber();

		$this->assertFalse($validator->passes('', '42280711405'));
	}

	public function testIncorrectChecksum() {
		$validator = new ValidatePersonalIdentityNumber();

		$this->assertFalse($validator->passes('', '00000000001'));
	}

	public function testIncorrectLength() {
		$validator = new ValidatePersonalIdentityNumber();

		$this->assertFalse($validator->passes('', '123'));
	}

	public function testIncorrectLengthCorrectChecksum() {
		$validator = new ValidatePersonalIdentityNumber();

		$this->assertFalse($validator->passes('', '8236'));
	}
}
