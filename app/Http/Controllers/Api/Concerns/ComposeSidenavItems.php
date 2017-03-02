<?php

namespace App\Http\Controllers\Api\Concerns;

trait ComposeSidenavItems
{
	function composeItem(string $type, int $id, string $name, array $ancestors = [], array $meta = [])
	{
		return [
			'type' => $type,
			'id' => $id,
			'name' => $name,
			'ancestors' => $ancestors,
			'meta' => $meta,
		];
	}
}
