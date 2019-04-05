<?php

namespace Tests;

use DB;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
	use CreatesApplication;

	/**
	 * https://laracasts.com/discuss/channels/testing/how-to-assert-that-the-database-contains-a-value-stored-as-json#reply=416476
	 *
	 * @param array $array
	 * @return Expression
	 */
	protected function castToJson(array $array): Expression
	{
		$json = addslashes(json_encode($array));

		return DB::raw("CAST('{$json}' AS JSON)");
	}
}
