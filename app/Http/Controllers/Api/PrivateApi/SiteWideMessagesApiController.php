<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\UpdateSiteWideMessage;
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
		$user = \Auth::user();

		if (!$user->isAdmin() && $user->id != $userId) {
			return $this->respondForbidden();
		}

		$data = SiteWideMessage::where('user_id', $userId)
			->where('start_date', "<=", Carbon::now())
			->where('end_date', ">=", Carbon::now())
			->whereNull('read_at')
			->get();

		return $this->transformAndRespond($data);
	}

	public function put(UpdateSiteWideMessage $request, $messageId) {
		$siteWideMessage = SiteWideMessage::find($messageId);

		if (empty($siteWideMessage)) {
			return $this->respondNotFound();
		}

		if (!empty($request->read_at)) {
			$siteWideMessage->read_at = Carbon::parse($request->read_at);
		}

		$siteWideMessage->save();

		return $this->transformAndRespond($siteWideMessage);
	}
}
