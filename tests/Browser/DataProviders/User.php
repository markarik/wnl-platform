<?php

namespace Tests\Browser\DataProviders;


class User
{
	const USER_EMAIL_1 = 'jlkarminski@gmail.com';
	const USER_PASSWORD_1 = 'secret';
	const USER_NAME_1 = 'Kuba';
	const USER_LAST_NAME_1 = 'Karmiński';

	const USER_EMAIL_2 = 'adamkarminski@gmail.com';
	const USER_PASSWORD_2 = 'secret';
	const USER_NAME_2 = 'Adam';
	const USER_LAST_NAME_2 = 'Karmiński';

	public static function userProvider()
	{
		return [
			[self::USER_EMAIL_1, self::USER_PASSWORD_1, self::USER_NAME_1]
		];
	}
}
