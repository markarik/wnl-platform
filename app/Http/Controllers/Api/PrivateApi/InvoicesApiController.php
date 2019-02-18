<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicesApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.invoices');
	}

	public function getFile($id)
	{
		$user = Auth::user();

		$invoice = Invoice::find($id);

		if (!$invoice) {
			return $this->respondNotFound('Invoice not found');
		}

		if (!$user->can('view', $invoice)) {
			return $this->respondForbidden();
		}

		if(!\Storage::exists($invoice->file_path)) {
			return $this->respondNotFound('File not found');
		}

		$filename = $invoice->number_slugged . '.pdf';

		return response(\Storage::drive()->get($invoice->file_path), 200)
			->header('Content-type', 'application/pdf')
			->header('Content-Disposition', sprintf('attachment; filename=%s', $filename))
			->header('Cache-Control', 'no-store, no-cache');
	}
}
