<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use Carbon\Carbon;
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

	public function getUserAvailabileLessons(Request $request)
	{
		$user = \Auth::user();
		$lessons = UserLessonAvailability::where('user_id', $user->id)->get();
		$data = $this->transform($lessons);

		return $this->respondOk($data);
	}

	public function put(UpdateUserLessonAvailability $request)
	{
		$userLessonAvailability = UserLessonAvailability::find($request->route('id'));

		if (!$userLessonAvailability) {
			return $this->respondNotFound();
		}

		$userLessonAvailability->update([
			'start_date' => Carbon::parse($request->input('date')),
		]);

		return $this->respondOk();
	}
}
