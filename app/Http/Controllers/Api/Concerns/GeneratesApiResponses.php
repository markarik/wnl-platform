<?php

namespace App\Http\Controllers\Api\Concerns;


trait GeneratesApiResponses
{
	protected function respondNotFound()
	{
		return response('Not Found', 404);
	}
}