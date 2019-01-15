<?php
namespace Checks\DataIntegrity;

use App\Exceptions\DataIntegrityException;

abstract class DataIntegrityCheck {

	protected function handleError($name, $context) {
		if (env('APP_ENV') === 'dev') {
			dump($context);
			return;
		}

		$sentryClient = new \Raven_Client(env('SENTRY_DSN'));
		$sentryClient->captureException(new DataIntegrityException("DATA INTEGRITY CHECK FAILED: $name"), [
			'extra' => $context
		]);
	}

	public abstract function check();
}
