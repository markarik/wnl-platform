<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\SiteWideMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SiteWideMessagesApiController extends ApiController
{

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.site-wide-messages');
	}

	public function getForUser($userId) {
		$data = SiteWideMessage::where('user_id', $userId)
			->where('start_date', "<=", Carbon::now())
			->where('end_date', ">=", Carbon::now())
			->get();

		return $this->transformAndRespond($data);
	}
}
