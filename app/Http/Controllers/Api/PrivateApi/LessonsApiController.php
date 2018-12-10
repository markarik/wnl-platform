<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateLesson;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.lessons');
	}

    public function post(UpdateLesson $request) {
        $lesson = Lesson::create($request->all());

        return $this->transformAndRespond($lesson);
    }

	public function put(UpdateLesson $request)
	{
		$lesson = Lesson::find($request->route('id'));

		if (!$lesson) {
			return $this->respondNotFound();
		}

		$lesson->update($request->all());

		return $this->respondOk();
	}
}
