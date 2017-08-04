<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use Auth;
use App\Models\User;
use App\Models\Reaction;
use App\Models\Category;
use App\Models\Reactable;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\ReactableTransformer;


class UserReactionsApiController extends ApiController
{
	public function getReactions($id, $type = null)
	{
		$user = User::fetch($id);
		if (!$user) {
			return $this->respondNotFound();
		}

		if (Auth::user()->id !== $user->id) {
			return $this->respondUnauthorized();
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
			return $this->respondUnauthorized();
		}

		$reactablesBuilder = Reactable::where(['user_id' => $user->id]);

		$reaction = Reaction::type($type);
		$reactablesBuilder->where('reaction_id', $reaction->id);

		$reactables = $reactablesBuilder->get();
		$categories = Category::all();
		$categorizedReactables = [];

		$grouped = $reactables->groupBy('reactable_type');

		foreach ($grouped as $key => $item) {
			$grouped->{$key} = $item->keyBy('reactable_id');

			$models = $key::with(['tags'])
				->whereIn('id', $item->pluck('reactable_id'))
				->get();

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
				if ($tags->contains($category->name)) {
					$categorizedReactables[$category->name][] = $reactable;
				}
			}
		}

		return $this->json(['reactions' => $categorizedReactables]);
	}
}
