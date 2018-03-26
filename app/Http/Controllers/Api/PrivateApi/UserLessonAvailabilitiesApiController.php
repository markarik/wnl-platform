<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
use Cache;
use Illuminate\Http\Request;
use League\Fractal\Resource\Item;
use App\Http\Requests\Course\UpdateUserLessonAvailability;
use App\Models\UserLessonAvailability;

class UserLessonAvailabilitiesApiController extends ApiController
{

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.user-lesson-availabilities');
	}

	public function put(UpdateUserLessonAvailability $request)
	{
		$user = \Auth::user();
		$userLessonAvailability = UserLessonAvailability::where([
			'lesson_id' => $request->id,
			'user_id' => $user->id
		])->first();


		if (!$userLessonAvailability) {
			return $this->respondNotFound();
		}

		$userLessonAvailability->update([
			'start_date' => Carbon::parse($request->input('date')),
			]);

		Cache::tags("user-$user->id")->flush();

		return $this->respondOk();
	}
}
