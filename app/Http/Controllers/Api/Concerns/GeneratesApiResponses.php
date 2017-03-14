<?php

namespace App\Http\Controllers\Api\Concerns;


trait GeneratesApiResponses
{
	protected function respondOk()
	{
		return response('OK', 200);
	}

	protected function respondCreated()
	{
		return response('Created', 201);
	}

	protected function respondUnauthorized()
	{
		return response('Unauthorized', 401);
	}

	protected function respondNotFound()
	{
		return response('Not Found', 404);
	}

	protected function respondInternalError()
	{
		return response('Internal Error', 500);
	}
}