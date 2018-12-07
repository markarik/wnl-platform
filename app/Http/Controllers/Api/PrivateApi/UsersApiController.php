<?php namespace App\Http\Controllers\Api\PrivateApi;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\CommentTransformer;
use App\Http\Controllers\Api\Transformers\QnaAnswerTransformer;
use App\Http\Controllers\Api\Transformers\QnaQuestionTransformer;
use App\Http\Requests\User\PostUser;
use App\Http\Requests\User\UpdateUser;
use App\Models\QnaAnswer;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;

class UsersApiController extends ApiController
{
	public function __construct(Request $request)
	{
		parent::__construct($request);
		$this->resourceName = config('papi.resources.users');
	}

	public function put(UpdateUser $request, $userId)
	{
		$user = User::find($userId);

		if (empty($user)) {
			return $this->respondNotFound();
		}

		$user->first_name = $request->get('first_name');
		$user->last_name = $request->get('last_name');
		$user->email = $request->get('email');

		if ($request->get('password')) {
			$user->password = bcrypt($request->get('password'));
		}

		$user->roles()->sync($request->get('roles'));

		$user->save();

		return $this->respondOk();
	}

	public function forget(Request $request)
	{
		$user = Auth::user();
		$currentUserId = $request->userId;
		$password = $request->password;

		if ($user->id !== (int) $currentUserId) {
			return $this->respondForbidden('unauthorized');
		}

		if (!\Hash::check($password, $user->password)) {
			return $this->respondInvalidInput('wrong_password');
		}

		$user->forget();

		return $this->respondOk();
	}

	public function post(PostUser $request) {
		$user = User::create([
			'first_name' => $request->get('first_name'),
			'last_name' => $request->get('last_name'),
			'email' => $request->get('email'),
			'password' => bcrypt($request->get('password'))
		]);

		$user->roles()->sync($request->get('roles'));

		return $this->respondOk();
	}

	public function getComments(Request $request, $userId) {
		$user = User::fetch($userId);

		if ($user->id !== Auth::user()->id) {
			return $this->respondForbidden();
		}

		$resource = new Collection($user->comments, new CommentTransformer(), config('papi.resources.comments'));
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function getQnaAnswers(Request $request, $userId) {
		$user = User::fetch($userId);

		if ($user->id !== Auth::user()->id) {
			return $this->respondForbidden();
		}

		$resource = new Collection($user->qnaAnswers, new QnaAnswerTransformer(), config('papi.resources.qna-answers'));
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function getQnaQuestions(Request $request, $userId) {
		$user = User::fetch($userId);

		if ($user->id !== Auth::user()->id) {
			return $this->respondForbidden();
		}

		$userQnaQuestions = $user->qnaQuestions ?? [];
		$resource = new Collection($userQnaQuestions, new QnaQuestionTransformer(), config('papi.resources.qna-questions'));
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}
}
