<?php

namespace Tests\Browser\DataProviders;


class User
{
	public static function userProvider()
	{
		return [
			['jlkarminski@gmail.com', 'secret', 'Kuba']
		];
	}
}