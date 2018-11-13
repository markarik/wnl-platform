<?php


use App\Models\Role;

class ExampleData
{
    const ROLES = [
        Role::ROLE_ADMIN,
        Role::ROLE_MODERATOR,
        'workshop-participant',
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
            'name' => 'Kurs stacjonarny',
            'invoice_name' => 'Dostęp do platformy e-learningowej oraz udział w spotkaniach warsztatowych w ramach kursu "Więcej niż LEK", przygotowującego do Lekarskiego Egzaminu Końcowego',
            'price' => 2200.00,
            'slug' => 'wnl-online-onsite',
            'quantity' => 50,
            'initial' => 50,
        ],
        [
            'name' => 'Kurs internetowy',
            'invoice_name' => 'Dostęp do platformy e-learningowej w ramach kursu "Więcej niż LEK", przygotowującego do Lekarskiego Egzaminu Końcowego',
            'price' => 1500.00,
            'slug' => 'wnl-online',
            'quantity' => 2500,
            'initial' => 2500,
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
