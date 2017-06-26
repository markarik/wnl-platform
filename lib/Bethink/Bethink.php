<?php

namespace Lib\Bethink;

class Bethink {

	/**
	 * Returns a URL to an asset based on APP_URL value from .env, not a
	 * request referrer (see: asset())
	 * @param string $assetPath A path to an asset without the leading slash
	 */
	public function appUrlAsset($assetPath) {
		return env('APP_URL') . "/{$assetPath}";
	}
}