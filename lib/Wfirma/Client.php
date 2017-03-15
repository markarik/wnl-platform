<?php

namespace Lib\Wfirma;

use App\Exceptions\ApiCallException;
use App\Exceptions\ApiNotFoundException;
use App\Exceptions\ApiAuthException;
use Log;
use GuzzleHttp\Client as Guzzle;

abstract class Client
{
	public $inputFormat = 'json';

	public $outputFormat = 'json';

	protected $resource;

	protected $action;

	private $username;

	private $password;

	private $baseURL;

	private $requestData;

	private $id;

	public function __construct()
	{
		$config = config('wfirma');
		$this->username = $config['login'];
		$this->password = $config['password'];
		$this->baseURL = $config['baseUrl'];
	}

	protected function call()
	{
		$curl = curl_init();
		//		echo( $this->requestData . "<br><br>" );
		curl_setopt($curl, CURLOPT_URL, $this->buildURL());
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->requestData);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["content-type: application/json"]);
		curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);

		$response = curl_exec($curl);
		$responseHeader = curl_getinfo($curl);
		$err = curl_error($curl);
		$decodedResponse = json_decode($response, true);

		if ($err) {
			throw new ApiCallException('cURL Error: ' . $err);
		}

		if ($decodedResponse['status']['code'] == 'AUTH') {
			throw new ApiAuthException('Wfirma authentication failed');
		}

		if ($decodedResponse['status']['code'] == 'COMPANY ID REQUIRED') {
			throw new ApiAuthException('Wfirma subscription problem');
		}

		if ($responseHeader['content_type'] == 'application/pdf') {
			return $response;
		}

		if ($decodedResponse['status']['code'] !== 'OK') {
			Log::error('wFirma API call failed, server response:' . $response);
			switch ($decodedResponse['status']['code']) {

				case
				'NOT FOUND' :
					throw new ApiNotFoundException();
					break;

				default:
					throw new ApiCallException('Response: ' . $response);
			}
		}

		return $decodedResponse;
	}

	protected function buildURL()
	{
		$params = [
			'inputFormat'  => $this->inputFormat,
			'outputFormat' => $this->outputFormat,
		];
		$params = array_map(function ($key, $val) {
			return $key . '=' . $val;
		}, array_keys($params), $params);
		$paramString = implode('&', $params);
		$url = $this->baseURL;
		$url .= '/' . $this->resource;
		$url .= '/' . $this->action;
		$url .= $this->id ? '/' . $this->id : '';
		$url .= '?' . $paramString;

		return $url;
	}

	public function add(array $data)
	{
		$requestData[$this->resource][][str_singular($this->resource)] = $data;
		$this->requestData = json_encode($requestData);
		$this->action = 'add';

		$response = $this->call();

		return $response[$this->resource][0][str_singular($this->resource)];
	}

	public function get(int $id)
	{
		$this->id = $id;
		$this->action = 'get';

		return $this->call();
	}

	public function download(int $id)
	{
		$this->id = $id;
		$this->action = 'download';

		return $this->call();
	}

	public function edit(int $id, array $data)
	{
		$this->id = $id;
		$requestData[$this->resource][][str_singular($this->resource)] = $data;
		$this->requestData = json_encode($requestData);
		$this->action = 'edit';

		return $this->call();
	}

	public function find($conditions = [], $list = false, $limit = 10, $page = 1, $order = null)
	{

		$s = ['=', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'IN'];
		$r = ['eq', 'ne', 'gt', 'lt', 'ge', 'le', 'like', 'not like', 'in'];

		foreach ($conditions as $condition) {
			$rq[$this->resource]['parameters']['conditions'][]['condition'] = [
				'field'    => $condition[0],
				'operator' => str_replace($s, $r, $condition[1]),
				'value'    => is_array($condition[2]) ? implode(',', $condition[2]) : $condition[2],
			];
		}
		$rq[$this->resource]['parameters']['page'] = $page;
		$rq[$this->resource]['parameters']['limit'] = $limit;
		$this->action = 'find';
		$this->requestData = json_encode($rq);

		$response = $this->call();
		if (!array_key_exists(0, $response[$this->resource])) {
			return false;
		}

		if (!$list) {
			return $response[$this->resource][0][str_singular($this->resource)];
		}

		return $response[$this->resource];
	}

	public static function testConnection()
	{
		try {
			// make a call here
			// do we need to test connection at all... ?
		} catch (ApiAuthException $ex) {
			return false;
		}

		return true;
	}

}