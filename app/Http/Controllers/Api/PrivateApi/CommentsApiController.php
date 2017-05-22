<?php


namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\CommentTransformer;
use App\Http\Requests\PostComment;
use App\Http\Requests\UpdateComment;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Resource\Item;

class CommentsApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.comments');
	}

	public function post(PostComment $request)
	{
		$user = Auth::user();

		$modelName = self::getResourceModel($request->get('commentable_resource'));
		$commentable = $modelName::find($request->get('commentable_id'));

		if (!$commentable) {
			return $this->respondNotFound();
		}

		$comment = $commentable->comments()->create([
			'user_id' => $user->id,
			'text'    => $request->input('text'),
		]);

		$resource = new Item($comment, new CommentTransformer, $this->resourceName);
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function put(UpdateComment $request)
	{
		$comment = Comment::find($request->route('id'));

		if (!$comment) {
			return $this->respondNotFound();
		}

		$comment->update([
			'text' => $request->input('text'),
		]);

		return $this->respondOk();
	}
}
