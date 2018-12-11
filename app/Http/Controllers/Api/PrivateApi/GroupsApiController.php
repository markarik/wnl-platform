<?php namespace App\Http\Controllers\Api\PrivateApi;


use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Course\UpdateGroup;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.groups');
	}

	public function post(UpdateGroup $request) {
		$group = Group::create([
			'name' => $request->get('name'),
			'course_id' => 1,
		]);

		return $this->transformAndRespond($group);
	}

	public function put(UpdateGroup $request, $id) {
		$group = Group::find($id);

		if (empty($group)) {
			return $this->respondNotFound();
		}

		$group->update($request->all());

		foreach($group->lessons as $lesson) {
			$lesson->order_number = array_search($lesson->id, $request->lessons) + 1;
			$lesson->save();
		}

		return $this->transformAndRespond($group);
	}
}