<?php

return [
	'proforma_series'   => env('INVOICE_PROFORMA_SERIES', 'PROFORMA/DEF'),
	'advance_series'    => env('INVOICE_ADVANCE_SERIES', 'F-ZAL/DEF'),
	'final_series'      => env('INVOICE_FINAL_SERIES', 'FK/DEF'),
	'vat_series'        => env('INVOICE_VAT_SERIES', 'FV/DEF'),
	'corrective_series' => env('INVOICE_CORRECTIVE_SERIES', 'KOR/DEF'),
];
