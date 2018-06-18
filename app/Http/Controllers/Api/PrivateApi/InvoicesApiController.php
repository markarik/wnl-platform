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

	public function get($id)
	{
		$user = Auth::user();

		$invoice = Invoice::find($id);

		if (empty($invoice)) {
			return $this->respondNotFound('Invoice not found');
		}

		if (!$user->can('view', $invoice)) {
			return $this->respondForbidden();
		}

		if(!file_exists($invoice->file_path)) {
			return $this->respondNotFound('File not found');
		}

		return response()->download($invoice->file_path, $invoice->name, [
			"Content-Type: application/pdf",
			"Content-disposition: attachment;filename=\"{$invoice->name}\""
		]);
	}
}
