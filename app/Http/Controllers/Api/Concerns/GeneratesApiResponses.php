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
			$data = [];
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

	protected function respondInvalidInput($message = '', $data = [])
	{
		return $this
			->setMessage($message ?? 'Invalid input')
			->setStatusCode(400)
			->json(array_merge($data, ['message' => $message]));
	}

	protected function respondUnauthorized()
	{
		return $this
			->setMessage($message ?? 'Unauthorized')
			->setStatusCode(401)
			->json();
	}

	protected function respondForbidden($message = 'Forbidden', $data = [])
	{
		return $this
			->setMessage($message)
			->setStatusCode(403)
			->json(array_merge($data, ['message' => $message]));
	}

	protected function respondNotFound($message = null)
	{
		return $this
			->setMessage($message ?? 'Not Found')
			->setStatusCode(404)
			->json();
	}

	protected function respondUnprocessableEntity($data = [], $message = '')
	{
		return $this
			->setMessage($message ?? 'Unprocessable Entity')
			->setStatusCode(422)
			->json($data);
	}

	protected function respondNotImplemented($message = null)
	{
		return $this
			->setMessage($message ?? 'Not Implemented')
			->setStatusCode(501)
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
