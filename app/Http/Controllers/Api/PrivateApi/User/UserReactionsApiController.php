<?php namespace App\Http\Controllers\Api\PrivateApi\User;

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

		foreach($categories as $category) {
			$categorizedReactables[$category->name] = [];
		}

		foreach ($reactables as $reactable) {
			$id = $reactable->reactable_id;
			$type = $reactable->reactable_type;
			$model = $type::find($id);

			$tagNames = $model->tags->pluck('name');

			foreach($tagNames as $tagName) {
				if (array_key_exists($tagName, $categorizedReactables)) {
					$categorizedReactables[$category->name][] = $reactable;
				}
			}
		}

		return view('layouts.app');
		return $this->json(['reactions' => $categorizedReactables]);
	}
}