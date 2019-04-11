<?php


use App\Models\Role;

class ExampleData
{
	const ROLES = [
		Role::ROLE_ADMIN,
		Role::ROLE_MODERATOR,
	];

	const USERS = [
		[
			'first_name' => 'Kuba',
			'last_name' => 'Karmiński',
			'email' => 'kuba.karminski@bethink.pl',
			'roles' => self::ROLES,
		],
		[
			'first_name' => 'Adam',
			'last_name' => 'Karmiński',
			'email' => 'adam.karminski@bethink.pl',
			'roles' => self::ROLES,
		],
		[
			'first_name' => 'Przemysław',
			'last_name' => 'Ferkaluk',
			'email' => 'przemyslaw.ferkaluk@bethink.pl',
			'roles' => self::ROLES,
		],
		[
			'first_name' => 'Bogna',
			'last_name' => 'Knychała',
			'email' => 'bogna.knychala@bethink.pl',
			'roles' => self::ROLES,
		],
		[
			'first_name' => 'Pierwszy',
			'last_name' => 'Człowiek',
			'email' => 'pierwszy.czlowiek@bethink.pl',
			'roles' => [],
		],
	];

	const PRODUCTS = [
		[
			'name' => 'Album map myśli',
			'invoice_name' => 'Album map myśli - materiały do kursu przygotowującego do Lekarskiego Egzaminu Końcowego',
			'price' => 300.00,
			'slug' => 'wnl-album',
			'quantity' => 100,
			'initial' => 100,
			'has_instalments' => false
		],
		[
			'name' => 'Kurs internetowy',
			'invoice_name' => 'Dostęp do platformy e-learningowej w ramach kursu "Więcej niż LEK", przygotowującego do Lekarskiego Egzaminu Końcowego',
			'price' => 1500.00,
			'slug' => 'wnl-online',
			'quantity' => 2500,
			'initial' => 2500,
			'has_instalments' => true
		]
	];

	const PAYMENT_METHODS = [
		[
			'slug' => 'transfer',
		],
		[
			'slug' => 'online',
		],
		[
			'slug' => 'instalments',
		],
	];

}
