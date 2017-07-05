<?php

namespace App\Http\Controllers\Api\Concerns;


trait GeneratesApiResponses
{
	protected $statusCode = 200;
	protected $headers = [];
	protected $message = 'OK';

	protected function setStatusCode($code)
	{
		$this->statusCode = $code;

		return $this;
	}

	protected function setMessage($message)
	{
		$this->message = $message;

		return $this;
	}

	protected function json($data = [])
	{
		if (empty($data)) {
			$data = [
				'message'     => $this->message,
				'status_code' => $this->statusCode,
			];
		}

		return response()->json($data, $this->statusCode, $this->headers);
	}

	protected function respondOk($data = [])
	{
		return $this
			->setMessage($message ?? 'OK')
			->setStatusCode(200)
			->json($data);
	}

	protected function respondCreated()
	{
		return $this
			->setMessage($message ?? 'Created')
			->setStatusCode(201)
			->json();
	}

	protected function respondNoContent()
	{
		return $this
			->setMessage($message ?? 'No Content')
			->setStatusCode(204)
			->json();
	}

	protected function respondInvalidInput($data = [], $message = '')
	{
		return $this
			->setMessage($message ?? 'Invalid input')
			->setStatusCode(400)
			->json($data);
	}

	protected function respondUnauthorized()
	{
		return $this
			->setMessage($message ?? 'Unauthorized')
			->setStatusCode(401)
			->json();
	}

	protected function respondForbidden()
	{
		return $this
			->setMessage($message ?? 'Forbidden')
			->setStatusCode(403)
			->json();
	}

	protected function respondNotFound($message = null)
	{
		return $this
			->setMessage($message ?? 'Not Found')
			->setStatusCode(404)
			->json();
	}

	protected function respondInternalError()
	{
		return $this
			->setMessage($message ?? 'Internal Error')
			->setStatusCode(500)
			->json();
	}
}