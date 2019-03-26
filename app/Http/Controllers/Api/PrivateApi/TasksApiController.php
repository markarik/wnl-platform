<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Category;


class TasksApiController extends ApiController
{
	static $AVAILABLE_FILTERS = [
		'task-course_structure',
		'task-assignee',
		'task-subject_type'
	];

	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.tasks');
	}

	public function get($id)
	{
		$this->authorize('get', Task::class);

		return parent::get($id);
	}

	public function patch(Request $request)
	{
		$this->authorize('update', Task::class);
		$id = $request->route('id');

		$task = Task::find($id);
		if (!$task) {
			return $this->respondNotFound();
		}
		$task->update($request->all());
		$data = $this->transform($task);

		return $this->respondOk($data);
	}
}
