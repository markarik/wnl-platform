<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Task;
use Illuminate\Http\Request;

class TasksApiController extends ApiController
{
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

	public function patch(Request $request) {
		$this->authorize('update', Task::class);
		$id = $request->route('id');

		$task = Task::find($id);
		$task->status = $request->status;
		$task->save();

		return $this->json(['data' => $task]);
	}
}
