<?php

namespace App\Contracts;

use GuzzleHttp\Client;

class Requests {

	public $client;

	public function __construct(Client $client) {
		$this->client = $client;
	}

	public function request($method, $url, $headers, $body) {
		$this->client->request($method, $url, [
			'headers' => $headers,
			'json' => $body
		]);
	}
}
