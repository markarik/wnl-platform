<?php

namespace App\Models\Concerns;


use Facades\Lib\Bethink\Bethink;

trait IsResource
{
	public function getResourceName()
	{
		return Bethink::getResourceByClassInstance($this);
	}
}
