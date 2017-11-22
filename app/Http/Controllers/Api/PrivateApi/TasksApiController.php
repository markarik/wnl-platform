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
		$rootCategories = Category::where('parent_id', null)->get(['id', 'name']);

		foreach($rootCategories as $rootCategory) {
			$rootCategory['categories'] = Category::where('parent_id', $rootCategory->id)->get(['id', 'name']);
		}

		// Be smarter and think how to not hardcode it
		$rootCategories[] = [
			'id' => -1,
			'name' => 'Pomoc',
			'categories' => [
				['name' => 'Pomoc techniczna'],
				['name' => 'Błędy'],
				['name' => 'Pomoc w nauce'],
				['name' => 'Nowe funkcje'],
				['name' => 'Sugestie'],
			]
		];

		return $this->json($rootCategories);
	}
}
