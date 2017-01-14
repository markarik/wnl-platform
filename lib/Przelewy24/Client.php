<?php

namespace Lib\Przelewy24;

use App\Exceptions\ApiCallException;

class Client
{

	private $url;

	private $data;

	private $merchantId;

	private $posId;

	private $amount;

	private $currency;

	private $orderId;

	private $sign;

	public function __construct() {
		$this->merchantId = config('przelewy24.merchant_id');
		$this->posId = config('przelewy24.merchant_id');
		$this->currency = 'PLN';
	}


	private function call() {

		$data = [];

		foreach ($this->data as $key => $value) {
			$data[] = $key . "=" . urlencode($value);
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, join('&', $data));

		$response = curl_exec($curl);
		$responseHeader = curl_getinfo($curl);
		curl_close($curl);

		if ($responseHeader["http_code"] !== 200) {
			throw new ApiCallException('Request failed. Response code was:' . $responseHeader['http_code']);
		}

		return $response;
	}

	public function verifyTransaction($sessionId, $amount, $orderId) {
		$this->url = config('przelewy24.verify_url');

		$this->data = [
			'p24_merchant_id' => $this->merchantId,
			'p24_pos_id'      => $this->posId,
			'p24_session_id'  => $sessionId,
			'p24_amount'      => $amount,
			'p24_currency'    => $this->currency,
			'p24_order_id'    => $orderId,
			'p24_sign'        => self::generateChecksum($sessionId, $amount),
		];

		$response = $this->call();

		if ($response == 'error=0') {
			return true;
		}

		\Log::error('Transaction verification failed. Server response:' . $response);

		return false;

	}

	public function testConnections() {

	}

	public static function generateChecksum($sessionId, $amount) {

		$values = [
			$sessionId,
			config('przelewy24.merchant_id'),
			$amount,
			'PLN',
			config('przelewy24.crc_key'),
		];

		return md5(implode('|', $values));
	}

}