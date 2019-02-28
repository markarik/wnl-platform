<?php


namespace Tests\Api;


use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
	const BASE = '/papi/v2';

	/**
	 * Build target URL
	 *
	 * @param $path
	 * @return string
	 */
	protected function url($path)
	{
		return self::BASE . $path;
	}
}
