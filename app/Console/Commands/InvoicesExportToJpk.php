<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Lib\Invoice\Invoice as LibInvoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InvoicesExportToJpk extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'invoices:jpk {from} {to} {--filename=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export invoices to JPK format.';

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
		$dateFrom = Carbon::parse($this->argument('from'))->startOfDay();
		$dateTo = Carbon::parse($this->argument('to'))->endOfDay();

		$invoices = Invoice::with(['order', 'order.user', 'order.product'])
			->whereIn('series', [
				LibInvoice::CORRECTIVE_SERIES_NAME,
				LibInvoice::VAT_SERIES_NAME,
				LibInvoice::ADVANCE_SERIES_NAME,
			])
			->whereBetween('created_at', [$dateFrom, $dateTo])
			->get();

		$data = [];
		$data['tns:Podmiot1'] = [
			'tns:NIP' => '7811943756',
			'tns:PelnaNazwa' => 'bethink Sp. z o.o.',
			'tns:Email' => 'info@bethink.pl',
		];
		$data['tns:SprzedazCtrl'] = [
			'tns:LiczbaWierszySprzedazy' => $invoices->count(),
			'tns:PodatekNalezny' => number_format($invoices->sum('vat_amount'), 2, '.', ''),
		];
		$data['tns:SprzedazWiersz'] = [];

		foreach ($invoices as $invoice) {
			$user = $invoice->order->user;
			$isCompany = $invoice->order->user->invoice && $user->invoice_nip;

			$row = [
				'tns:LpSprzedazy' => $invoice->id,
				'tns:NrKontrahenta' => $isCompany ? $user->invoice_nip : 'brak',
				'tns:NazwaKontrahenta' => $isCompany ? $user->invoice_name : $user->full_name,
				'tns:AdresKontrahenta' => $isCompany ? $user->invoice_address : $user->full_address,
				'tns:DowodSprzedazy' => $invoice->full_number,
				'tns:DataWystawienia' => $invoice->created_at->format('Y-m-d'),
				'tns:DataSprzedazy' => $invoice->created_at->format('Y-m-d'),
			];

			$net = number_format($invoice->net_value, 2, '.', '');
			$vat = number_format($invoice->vat_amount, 2, '.', '');

			if ($invoice->order->product->vat_rate === 23.00) {
				$row['tns:K_19'] = $net;
				$row['tns:K_20'] = $vat;
			}

			if ($invoice->order->product->vat_rate === 5.00) {
				$row['tns:K_15'] = $net;
				$row['tns:K_16'] = $vat;
			}

			if ($invoice->order->product->vat_rate === 0.00) {
				$row['tns:K_10'] = $net;
			}

			array_push($data['tns:SprzedazWiersz'], $row);
		}

		$filename = $this->option('filename')
			?? sprintf('jpk-%s-%s.xml',
				$dateFrom->format('Ymd'),
				$dateTo->format('Ymd')
			);

		\Storage::put("exports/{$filename}", $this->toXml($data));

		return;
	}

	protected function toXml($data)
	{
		$dateFrom = Carbon::parse($this->argument('from'));
		$dateTo = Carbon::parse($this->argument('to'));

		$xmlData = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' .
			'<tns:JPK xmlns:etd="http://crd.gov.pl/xml/schematy/dziedzinowe/mf/2016/01/25/eD/DefinicjeTypy/" xmlns:tns="http://jpk.mf.gov.pl/wzor/2017/11/13/1113/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></tns:JPK>');

		$header = $xmlData->addChild('tns:Naglowek');
		$formCode = $header->addChild('tns:KodFormularza', 'JPK_VAT');
		$formCode->addAttribute('kodSystemowy', 'JPK_VAT (3)');
		$formCode->addAttribute('wersjaSchemy', '1-1');

		$header->addChild('tns:WariantFormularza', '3');
		$header->addChild('tns:CelZlozenia', '0');
		$header->addChild('tns:DataWytworzeniaJPK', Carbon::now()->toIso8601ZuluString());
		$header->addChild('tns:DataOd', $dateFrom->format('Y-m-d'));
		$header->addChild('tns:DataDo', $dateTo->subDay()->format('Y-m-d'));
		$header->addChild('tns:NazwaSystemu', 'InVooyce 9000');

		$simpleXml = $this->processXmlNode($data, $xmlData);

		// A weird hack to get the XML formatted,
		// I haven't found any other way to do that.
		$dom = dom_import_simplexml($simpleXml)->ownerDocument;
		$dom->formatOutput = true;
		$xml = $dom->saveXML();

		return $xml;
	}

	public function processXmlNode($data, $xmlNode)
	{
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				if (!$this->isAssoc($value) && !empty($value)) {
					foreach ($value as $item) {
						$subNode = $xmlNode->addChild($key);
						$this->processXmlNode($item, $subNode);
					}
				} else {
					$subNode = $xmlNode->addChild($key);
					$this->processXmlNode($value, $subNode);
				}
			} else {
				$xmlNode->addChild($key, $value);
			}
		}

		return $xmlNode;
	}

	protected function isAssoc(array $array)
	{
		if (array() === $array) return false;

		return array_keys($array) !== range(0, count($array) - 1);
	}
}
