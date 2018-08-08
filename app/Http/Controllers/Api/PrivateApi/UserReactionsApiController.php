<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\ReactableTransformer;
use App\Models\Category;
use App\Models\Reactable;
use App\Models\Reaction;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;


class UserReactionsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.reactions');
	}

	public function getReactions($id, $type = null)
	{
		$user = User::fetch($id);
		if (!$user) {
			return $this->respondNotFound();
		}

		if (Auth::user()->id !== $user->id) {
			return $this->respondForbidden();
		}

		$reactablesBuilder = Reactable::where(['user_id' => $user->id]);

		if ($type) {
			$reaction = Reaction::type($type);
			$reactablesBuilder->where('reaction_id', $reaction->id);
		}

		$reactables = $reactablesBuilder->get();

		$resource = new Collection($reactables, new ReactableTransformer, 'user_notifications');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->json(['reactions' => $data]);
	}

	public function getReactionsByCategory($id, $type)
	{
		$user = User::fetch($id);
		if (!$user) {
			return $this->respondNotFound();
		}

		if (Auth::user()->id !== $user->id) {
			return $this->respondForbidden();
		}

		$reactablesBuilder = Reactable::where(['user_id' => $user->id]);

		$reaction = Reaction::type($type);
		$reactablesBuilder->where('reaction_id', $reaction->id);

		$reactables = $reactablesBuilder->get(['reactable_id', 'reactable_type', 'id', 'reaction_id']);
		$categories = Category::whereNotNull('parent_id')->get();
		$categorizedReactables = [];

		$grouped = $reactables->groupBy('reactable_type');

		foreach ($grouped as $key => $item) {
			$grouped->{$key} = $item->keyBy('reactable_id');

			$models = $key::with(['tags'])
				->whereIn('id', $item->pluck('reactable_id'))
				->get(['id']);

			foreach ($models as $model) {
				$tags = $model->tags->pluck('name')->map(function ($el) {
					return trim($el);
				});
				$grouped->{$key}[$model->id]->reactableTags = $tags;
			}
		}

		foreach ($grouped->flatten() as $reactable) {
			$tags = $reactable->reactableTags;

			foreach ($categories as $category) {
				if ($tags !== null && $tags->contains($category->name)) {
					$categorizedReactables[$category->name][] = $reactable;
				}
			}
		}

		return $this->json(['reactions' => $categorizedReactables]);
	}
}
