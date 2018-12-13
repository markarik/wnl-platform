<?php
namespace Tests\DataIntegrity;

use App\Exceptions\DataIntegrityException;

abstract class DataIntegrityTestCase {

	protected function handleError($name, $context) {
		$sentryClient = new \Raven_Client(env('SENTRY_DSN'));
		$sentryClient->captureException(new DataIntegrityException("DATA INTEGRITY CHECK FAILED $name"), [
			'extra' => $context
		]);
	}

	public abstract function check();
}
