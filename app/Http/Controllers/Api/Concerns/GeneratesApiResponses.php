<?php

namespace App\Http\Controllers\Api\Concerns;


trait GeneratesApiResponses
{
	protected function respondOk($message = null)
	{
		$responseMessage = $message ?? 'OK';

		return response($responseMessage, 200);
	}

	protected function respondCreated($message = null)
	{
		$responseMessage = $message ?? 'Created';

		return response($responseMessage, 201);
	}

	protected function respondInvalidInput($message = null)
	{
		$responseMessage = $message ?? 'Invalid input';

		return response($responseMessage, 400);
	}

	protected function respondUnauthorized($message = null)
	{
		$responseMessage = $message ?? 'Unauthorized';

		return response($responseMessage, 401);
	}

	protected function respondNotFound($message = null)
	{
		$responseMessage = $message ?? 'Not Found';

		return response($responseMessage, 404);
	}

	protected function respondInternalError($message = null)
	{
		$responseMessage = $message ?? 'Internal Error';

		return response($responseMessage, 500);
	}
}