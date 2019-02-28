<?php

return [
	'proforma_series'   => env('INVOICE_PROFORMA_SERIES', 'PROFORMA'),
	'advance_series'    => env('INVOICE_ADVANCE_SERIES', 'F-ZAL'),
	'final_series'      => env('INVOICE_FINAL_SERIES', 'FK'),
	'vat_series'        => env('INVOICE_VAT_SERIES', 'FV'),
	'corrective_series' => env('INVOICE_CORRECTIVE_SERIES', 'KOR'),
];
