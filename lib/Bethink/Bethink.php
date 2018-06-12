<?php

namespace Lib\Bethink;

class Bethink
{

	/**
	 * Returns a URL to an asset based on APP_URL value from .env, not a
	 * request referrer (see: asset())
	 *
	 * @param string $assetPath A path to an asset without the leading slash
	 *
	 * @return string
	 */
	public function appUrlAsset($assetPath)
	{
		return env('APP_URL') . "/{$assetPath}";
	}

	public function getAssetPublicUrl($assetPath)
	{
		if(config('filesystems.default') === 's3') {
			return \Storage::url('public/' . $assetPath);
		}

		return env('APP_URL') . "/storage/{$assetPath}";
	}

	public function getResourceByClassInstance($class)
	{
		return snake_case(str_plural(class_basename($class)));
	}

	public function getTypeByNamespace($namespace)
	{
		return snake_case(str_replace('App\\Models\\', '', $namespace));
	}

	public function getTypeByClassInstance($class)
	{
		return snake_case(class_basename($class));
	}
}
