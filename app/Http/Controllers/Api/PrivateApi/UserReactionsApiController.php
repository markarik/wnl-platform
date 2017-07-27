<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\Transformers\ReactableTransformer;
use Auth;
use App\Models\User;
use App\Models\Reaction;
use App\Models\Reactable;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Controllers\Api\ApiController;
use League\Fractal\Resource\Collection;


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

	public function getReactionsByCategory($id, $type = null)
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
		$categories = Category::all();
		$categorizedReactables = [];

		foreach ($reactables as $reactable) {
			$id = $reactable->reactable_id;
			$type = $reactable->reactable_type;
			$model = $type::find($id);

			foreach($categories as $category) {
				$tag = Tag::where('name', $category->name)->first();
				if ($model->tags->contains($tag)) {
					$categorizedReactables[$category->name][] = $reactable;
				}
			}
		}

		return $this->json(['reactions' => $categorizedReactables]);
	}
}