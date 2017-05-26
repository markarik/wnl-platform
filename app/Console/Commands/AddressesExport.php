<?php

namespace App\Console\Commands;

use Storage;
use App\Models\User;
use Illuminate\Console\Command;

class AddressesExport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'order:export';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$users = User::wherehas('orders', function ($query) {
			return $query->where('paid', 1);
		})->get();

		$schema = $this->getSchema();

		$data = '';
		$data .= $this->printHeaders($schema);
		foreach ($users as $user) {
			$data .= $this->printLine($user, $schema);
		}

		Storage::put('exports/inpost.csv', $data);

		return;
	}

	public function printLine($user, $schema)
	{
		$schema['receiver_name'] = $user->first_name;
		$schema['receiver_surname'] = $user->last_name;
		$schema['receiver_email'] = $user->email;
		$schema['receiver_street'] = $user->address;
		$schema['receiver_postcode'] = $user->zip;
		$schema['receiver_city'] = $user->city;
		$schema['receiver_phone'] = $user->phone;
		$schema['user_reference_number'] = $user->id;

		return implode(',', $schema) . PHP_EOL;
	}

	public function printHeaders($schema)
	{
		return implode(',', array_keys($schema)) . PHP_EOL;
	}

	protected function getSchema()
	{
		return [
			'service'                => 'inpostkurier',
			'sender_name'            => 'bethink',
			'sender_surname'         => '',
			'sender_company'         => 'bethink sp. z o.o.',
			'sender_email'           => 'info@bethink.pl',
			'sender_street'          => 'Henryka Sienkiewicza 8/1',
			'sender_postcode'        => '60-817',
			'sender_city'            => 'Poznań',
			'sender_phone'           => '722100867',
			'sender_paczkomat'       => '',
			'sender_placowka'        => '',
			'receiver_type'          => 'private',
			'receiver_name'          => '#',
			'receiver_surname'       => '#',
			'receiver_email'         => '#',
			'receiver_street'        => '#',
			'receiver_postcode'      => '#',
			'receiver_city'          => '#',
			'receiver_phone'         => '#',
			'receiver_paczkomat'     => '',
			'receiver_alt_paczkomat' => '',
			'receiver_placowka'      => '',
			'user_reference_number'  => '#',
			'type'                   => 'package',
			'wrapping'               => '5',
			'shape'                  => '0',
			'weight'                 => '10',
			'width'                  => '40',
			'height'                 => '10',
			'depth'                  => '30',
			'value'                  => '300',
			'description'            => '',
			'poczta_value'           => '',
		];
	}
}
