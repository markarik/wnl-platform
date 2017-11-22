<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Category;


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

	public function query(Request $request)
	{
		$this->authorize('get', Task::class);

		return parent::query($request);
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

	public function filterList(Request $request)
	{
		$allCategories = Category::select('name')->get();
		$tags = Tag::select('id', 'name')
			->where(function($query) use ($allCategories) {
				$query->whereIn('name', $allCategories->pluck('name'));
			})
			// those tags represent Pomoc* views
			// Something better desperately needed
			->orWhereIn('id', [1,2,3,4,5])
			->orderBy('name')->get();

		return $this->respondOk($tags);
	}
}
