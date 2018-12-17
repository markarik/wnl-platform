<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Events\Comments\CommentRemoved;
use App\Events\Comments\CommentRestored;
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
		$comment = Comment::withTrashed()->find($request->route('id'));

		if (!$comment) {
			return $this->respondNotFound();
		}

		$statusResolved = $request->input('resolved');
		if (isset($statusResolved)) {
			if ($statusResolved) {
				$comment->delete();
				event(new CommentRemoved($comment, Auth::user()->id, 'resolved'));
			} else {
				$comment->restore();
				event(new CommentRestored($comment, Auth::user()->id));
			}
		} else {
			$comment->update([
				'text' => $request->input('text'),
			]);
		}

		return $this->respondOk();
	}

	public function query(Request $request) {
		$commentsQuery = Comment::select();

		if ($request->has('commentable_id')) {
			$commentableId = $request->get('commentable_id');
			$commentsQuery->where('commentable_id', $commentableId);
		}

		if ($request->has('commentable_type')) {
			$commentableType = $request->get('commentable_type');
			$commentsQuery->where('commentable_type', $commentableType);
		}

		if ($request->has('id')) {
			$commentsQuery->where('id', $request->get('comment_id'));
		}

		if ($request->has('user_id')) {
			$commentsQuery->where('user_id', $request->get('user_id'));
		}

		$comments = $commentsQuery->orderBy('id', 'asc')->get();

		return $this->transformAndRespond($comments);
	}
}
