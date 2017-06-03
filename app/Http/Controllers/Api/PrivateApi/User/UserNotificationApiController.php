<?php namespace App\Http\Controllers\Api\PrivateApi\User;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Notification;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\Transformers\NotificationTransformer;
use Symfony\Component\HttpFoundation\Request;

class UserNotificationApiController extends ApiController
{
	public function get($id)
	{
		$user = User::fetch($id);

		$notifications = $user->notifications;

		if (!$user || !$notifications) {
			return $this->respondNotFound();
		}

		if (Auth::user()->id !== $user->id) {
			return $this->respondUnauthorized();
		}

		$resource = new Collection($notifications, new NotificationTransformer, 'user_notifications');
		$data = $this->fractal->createData($resource)->toArray();

		return $this->respondOk($data);
	}

	public function patch(Request $request)
	{
		$user = User::fetch($request->route('id'));

		if (!$user) {
			return $this->respondNotFound();
		}

		if (Auth::user()->id !== $user->id) {
			return $this->respondUnauthorized();
		}

		Notification::where('notifiable_id', $user->id)
			->where('notifiable_type', 'App\Models\User')
			->update([
				'read_at' => Carbon::createFromTimestamp($request->input('read_at')),
			]);

		return $this->respondOk();
	}
}

