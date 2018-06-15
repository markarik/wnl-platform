<?php

namespace App\Console\Commands;

use Storage;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;

class AddressesExport extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'addresses:export
		{--I|ids= : Comma separated list of ids of orders to export}
		{--P|products= : Comma separated list of products to export}
		{--F|from= : A date from which you want the export}
		{--S|ship= : Should the script mark orders as exported to shipping? Set to 1 to do it.}
		{--A|all : Script will export addresses with all shipping statuses}
	';

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
		$products = $this->option('products');
		$ids = $this->option('ids');
		$from = $this->option('from');
		$ship = intval($this->option('ship'));
		$all = $this->option('all');

		if ($ids) {
			$ids = explode(',', $ids);
			$orders = Order::where('paid', 1)
				->whereIn('id', $ids);
		} elseif ($products) {
			$products = explode(',', $products);
			$orders = Order::where('paid', 1)
				->whereIn('product_id', $products);
		} else {
			$this->error('Specify IDs or ProductIDs to export orders.');
		}

		if (!$all) {
			$orders = $orders->where('shipping_status', 'new');
		}

		if ($from) {
			$from = Carbon::createFromFormat('Y-m-d H:i:s', $from);
			$orders = $orders->where('created_at', '>', $from);
		}

		if ($ship===1) {
			$orders->update(['shipping_status' => 'ordered']);
		}

		$orders = $orders->get();
		$schema = $this->getSchema();

		$data = '';
		$data .= $this->printHeaders($schema);
		foreach ($orders as $order) {
			$data .= $this->printLine($order, $schema);
		}

		Storage::put('exports/inpost_' . time() . '.csv', $data);

		return;
	}

	public function printLine($order, $schema)
	{
		$user = User::find($order->user_id);
		$coupon = Coupon::find($order->coupon_id);

		$address = $user->userAddress()->first();

		$schema['receiver_name'] = $address->recipient ?? $user->full_name;
		$schema['receiver_surname'] = '';
		$schema['receiver_email'] = $user->email;
		$schema['receiver_street'] = $address->street;
		$schema['receiver_postcode'] = $address->zip;
		$schema['receiver_city'] = $address->city;
		$schema['receiver_phone'] = $address->phone;
		$schema['user_reference_number'] = $order->id;
		$schema['description'] = $order->product_id;

		if ($coupon) {
			$schema['poczta_value'] = $coupon->name;
		}

		$fields = array_map(function ($element) {
			return '"' . $element . '"';
		}, $schema);

		return implode(';', $fields) . PHP_EOL;
	}

	public function printHeaders($schema)
	{
		$headers = array_map(function ($element) {
			return '"' . $element . '"';
		}, array_keys($schema));

		return implode(';', $headers) . PHP_EOL;
	}

	protected function getSchema()
	{
		return [
			'service'                => 'inpostkurier',
			'sender_name'            => 'bethink Sp. z o. o.',
			'sender_surname'         => '',
			'sender_company'         => 'bethink sp. z o. o.',
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
			'weight'                 => '2',
			'width'                  => '40',
			'height'                 => '10',
			'depth'                  => '30',
			'value'                  => '300',
			'description'            => '',
			'poczta_value'           => '',
		];
	}
}
